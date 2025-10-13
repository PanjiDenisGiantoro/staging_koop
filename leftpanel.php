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

        // admin.dashboard
        SubTitleBar("DASHBOARD");
        if (@$mn == 900) {
            $mn900 = "mm-collapse mm-show";
            $mu900 = "mm-active";
        } else {
            $mn900 = '';
            $mu900 = '';
        }
        echo '<li class="' . $mu900 . '">';
        TitleBarBlue("Papan Utama", 'mdi mdi-chart-pie');
        echo '<ul class="sub-menu ' . $mn900 . '" aria-expanded="true">';
        MenuLink("dashboard.php", "Koperasi", 900, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Kesihatan Koperasi", 900, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Anggota", 900, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Simpanan", 900, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Pembiayaan", 900, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Analisis Kewangan", 900, @$_REQUEST['vw']);
            // admin.dashboard.papanUtama.panduan
            echo '<li>';
            TitleBarBlue("Panduan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn901 . '" aria-expanded="true">';
            // MenuLink("blank.php", "Anggota", 901, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';
        

        // admin.anggota
        SubTitleBar("ANGGOTA");
        // admin.anggota.infoAnggota
        if (@$mn == 901) {
            $mn901 = "mm-collapse mm-show";
            $mu901 = "mm-active";
        } else {
            $mn901 = '';
            $mu901 = '';
        }
        echo '<li class="' . $mu901 . '">';
        TitleBarBlue("Informasi", 'mdi mdi-contacts');
        echo '<ul class="sub-menu ' . $mn901 . '" aria-expanded="true">';
        MenuLink("member.php", "Pengajuan", 901, @$_REQUEST['vw']);
        MenuLink("memberProfil.php", "Profil", 901, @$_REQUEST['vw']);
        MenuLink("memberT.php", "Berhenti", 901, @$_REQUEST['vw']);
            // admin.anggota.infoAnggota.laporan
            echo '<li>';
            TitleBarBlue("Laporan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn901 . '" aria-expanded="true">';
            MenuLink("reports.php?cat=A", "Umum", 901, @$_REQUEST['vw']);
            
            echo '</ul></li>';
        echo '</ul></li>';
        // admin.anggota.modalSimpanan
        if (@$mn == 902) {
            $mn902 = "mm-collapse mm-show";
            $mu902 = "mm-active";
        } else {
            $mn902 = '';
            $mu902 = '';
        }
        echo '<li class="' . $mu902 . '">';
        TitleBarBlue("Modal & Simpanan", 'mdi mdi-account-cash');
        echo '<ul class="sub-menu ' . $mn902 . '" aria-expanded="true">';
        MenuLink("memberStmtA.php", "Info Modal", 902, @$_REQUEST['vw']);
        MenuLink("loansimpanan1.php", "Info Simpanan", 902, @$_REQUEST['vw']);
        // // MenuLink("blank.php", "Jurnal Pindahan", 902, @$_REQUEST['vw']);
        //     // admin.anggota.modalSimpanan.pengeluaran
        //     echo '<li>';
        //     TitleBarBlue("Pengeluaran", 'mdi mdi-cash-minus');
        //     echo '<ul class="sub-menu ' . $mn902 . '" aria-expanded="true">';
        //     // MenuLink("blank.php", "Permohonan", 902, @$_REQUEST['vw']);
        //     MenuLink("vouchersList.php", "Baucer", 902, @$_REQUEST['vw']);
        //     echo '</ul></li>';
        //     // admin.anggota.modalSimpanan.penambahan
        //     echo '<li>';
        //     TitleBarBlue(" ", 'mdi mdi-cash-plus');
        //     echo '<ul class="sub-menu ' . $mn902 . '" aria-expanded="true">';
        //     // MenuLink("blank.php", "Permohonan", 902, @$_REQUEST['vw']);
        //     MenuLink("resitList.php", "Resit", 902, @$_REQUEST['vw']);
        //     echo '</ul></li>';
        // MenuLink("blank.php", "Transaksi Online", 902, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Proses Hibah Simpanan", 902, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Penyata Simpanan", 902, @$_REQUEST['vw']);
            // admin.anggota.modalSimpanan.laporan
            echo '<li>';
            TitleBarBlue("Laporan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn902 . '" aria-expanded="true">';
            MenuLink("rAllFeesShare.php", "Keseluruhan Wajib & Syer", 901, @$_REQUEST['vw']);
            MenuLink("reportsimpanan.php", "Simpanan", 902, @$_REQUEST['vw']);
            MenuLink("reports.php?cat=B", "Transaksi", 902, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';

        // admin anggota.simpananBerjangka
        if (@$mn == 903) {
            $mn903 = "mm-collapse mm-show";
            $mu903 = "mm-active";
        } else {
            $mn903 = '';
            $mu903 = '';
        }
        echo '<li class="' . $mu903 . '">';
        TitleBarBlue("Simpan Berjangka", 'mdi mdi-account-clock');
        echo '<ul class="sub-menu ' . $mn903 . '" aria-expanded="true">';
        // MenuLink("blank.php", "Info Akaun Simpanan", 903, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Jurnal Pindahan", 903, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Pengeluaran(Baucer)", 903, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Penambahan(Resit)", 903, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Proses Hibah", 903, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Transaksi Online", 903, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Penyata (Cert)", 903, @$_REQUEST['vw']);
            // admin.anggota.simpananBerjangka.laporan
            echo '<li>';
            TitleBarBlue("Laporan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn903 . '" aria-expanded="true">';
            // MenuLink("blank.php", "Simpanan Berjangka", 903, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Transaksi", 903, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';

        // admin.anggota.transaksiAnggota
        if (@$mn == 945) {
            $mn945 = "mm-collapse mm-show";
            $mu945 = "mm-active";
        } else {
            $mn945 = '';
            $mu945 = '';
        }
        echo '<li class="' . $mu945 . '">';
        TitleBarBlue("Transaksi", 'mdi mdi-contacts');
        echo '<ul class="sub-menu ' . $mn945 . '" aria-expanded="true">';
        MenuLink("journalsTransferList.php", "Jurnal Pindahan", 945, @$_REQUEST['vw']);
            // admin.anggota.infoAnggota.pengeluaran
            echo '<li>';
            TitleBarBlue("Pengeluaran", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn945 . '" aria-expanded="true">';
            // MenuLink("blank.php", "Permohonan", 945, @$_REQUEST['vw']);
            MenuLink("vouchersList.php?jenis=1", "Baucer", 945, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.anggota.infoAnggota.penambahan
            echo '<li>';
            TitleBarBlue("Penambahan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn945 . '" aria-expanded="true">';
            // MenuLink("blank.php", "Permohonan", 945, @$_REQUEST['vw']);
            MenuLink("resitList.php", "Resit", 945, @$_REQUEST['vw']);
            echo '</ul></li>';
        // MenuLink("blank.php", "Transaksi Online", 945, @$_REQUEST['vw']);
            // admin.anggota.infoAnggota.laporan
            echo '<li>';
            TitleBarBlue("Laporan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn945 . '" aria-expanded="true">';
            // MenuLink("blank.php", "Umum", 945, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Jurnal Pindahan", 945, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Urus Niaga", 945, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';

        // admin.pembiayaan
        SubTitleBar("PEMBIAYAAN");
        // admin.pembiayaan.infoPembiayaan
        if (@$mn == 904) {
            $mn904 = "mm-collapse mm-show";
            $mu904 = "mm-active";
        } else {
            $mn904 = '';
            $mu904 = '';
        }
        echo '<li class="' . $mu904 . '">';
        TitleBarBlue("Informasi", 'mdi mdi-cash-usd');
        echo '<ul class="sub-menu ' . $mn904 . '" aria-expanded="true">';
        MenuLink("loan.php", "Senarai Mohon", 904, @$_REQUEST['vw']);
        MenuLink("loanTable.php", "Senarai (Diluluskan)", 904, @$_REQUEST['vw']);
        MenuLink("loanTable.php", "Senarai (Selesai)", 904, @$_REQUEST['vw']);
            // admin.pembiayaan.infoPembiayaan.advancePayment
            echo '<li>';
            TitleBarBlue("Advance Payment", 'mdi mdi-contactless-payment-circle');
            echo '<ul class="sub-menu ' . $mn904 . '" aria-expanded="true">';
            MenuLink("advanSenarai.php", "Daftar Baru", 904, @$_REQUEST['vw']);
            MenuLink("advanAdLulus.php", "Lulusan", 904, @$_REQUEST['vw']);
            MenuLink("advanListSelesai.php", "Selesai", 904, @$_REQUEST['vw']);
            echo '</ul></li>';
        MenuLink("memberPotonganAll.php", "Potongan Gaji", 904, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.pembiayaan.transaksiPembiayaan
        if (@$mn == 944) {
            $mn944 = "mm-collapse mm-show";
            $mu944 = "mm-active";
        } else {
            $mn944 = '';
            $mu944 = '';
        }
        echo '<li class="' . $mu944 . '">';
        TitleBarBlue("Transaksi", 'mdi mdi-cash-usd');
        echo '<ul class="sub-menu ' . $mn944 . '" aria-expanded="true">';
        MenuLink("komoditi_add.php", "Belian Sijil Komoditi", 904, @$_REQUEST['vw']);
        MenuLink("journalsList.php", "Pengeluaran(Baucer)", 904, @$_REQUEST['vw']);
        MenuLink("resit.php?action=new&jenis=2", "Terimaan(Resit)", 904, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Jurnal Pindahan", 904, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.pembiayaan.laporan
        if (@$mn == 905) {
            $mn905 = "mm-collapse mm-show";
            $mu905 = "mm-active";
        } else {
            $mn905 = '';
            $mu905 = '';
        }
        echo '<li class="' . $mu905 . '">';
        TitleBarBlue("Laporan", 'mdi mdi-file');
        echo '<ul class="sub-menu ' . $mn905 . '" aria-expanded="true">';
        MenuLink("reports.php?cat=B", "Umum", 905, @$_REQUEST['vw']);
        MenuLink("rloanApproved.php", "Kelulusan", 905, @$_REQUEST['vw']);
        MenuLink("rptAgingLoan.php", "Aging", 905, @$_REQUEST['vw']);
        echo '</ul></li>';
        

        // admin.hutangLapuk
        SubTitleBar("HUTANG MACET");
        // admin.hutangLapuk.infoNPFPembiayaan
        if (@$mn == 906) {
            $mn906 = "mm-collapse mm-show";
            $mu906 = "mm-active";
        } else {
            $mn906 = '';
            $mu906 = '';
        }
        echo '<li class="' . $mu906 . '">';
        TitleBarBlue("Informasi (NPF)", 'mdi mdi-credit-card-outline');
        echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
        MenuLink("memberProfilHL.php", "Profil", 906, @$_REQUEST['vw']);
        MenuLink("loanTableHL.php", "Senarai Pembiayaan", 906, @$_REQUEST['vw']);
        MenuLink("resitListHL.php", "Terima Pembiayaan (Resit)", 906, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Taqwid", 906, @$_REQUEST['vw']);
        MenuLink("memberStmtHL.php", "Penyata", 906, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.hutangLapuk.Laporan
        if (@$mn == 907) {
            $mn907 = "mm-collapse mm-show";
            $mu907 = "mm-active";
        } else {
            $mn907 = '';
            $mu907 = '';
        }
        echo '<li class="' . $mu907 . '">';
        TitleBarBlue("Laporan", 'mdi mdi-file');
        echo '<ul class="sub-menu ' . $mn907 . '" aria-expanded="true">';
        // MenuLink("blank.php", "Umum", 907, @$_REQUEST['vw']);
        MenuLink("rpthutanglapuk.php", "> 12", 907, @$_REQUEST['vw']);
        MenuLink("rpthutanglapuk.php", "< 12", 907, @$_REQUEST['vw']);
        echo '</ul></li>';
        
        
        // admin.akaun
        SubTitleBar("AKUN & KEUANGAN");
        // admin.akaun.master
        if (@$mn == 908) {
            $mn908 = "mm-collapse mm-show";
            $mu908 = "mm-active";
        } else {
            $mn908 = '';
            $mu908 = '';
        }
        echo '<li class="' . $mu908 . '">';
        TitleBarBlue("Master", 'mdi mdi-cube');
        echo '<ul class="sub-menu ' . $mn908 . '" aria-expanded="true">';
            // admin.akaun.master.tatapanAkaun
            // echo '<li>';
            // TitleBarBlue("Tatapan Akaun", 'mdi mdi-cube');
            // echo '<ul class="sub-menu ' . $mn908 . '" aria-expanded="true">';
            // // MenuLink("blank.php", "Kod Object/Akaun", 908, @$_REQUEST['vw']);
            // // MenuLink("blank.php", "Jenis Pembiayaan", 908, @$_REQUEST['vw']);
            // echo '</ul></li>';
        if ((get_session("Cookie_userName") == 'superadmin') or (get_session("Cookie_userName") == 'admin') or (get_session("Cookie_groupID") == '3')) {
            if (@$mn == 904) {
                $mn904 = "mm-collapse mm-show";
                $mu904 = "mm-active";
            } else {
                $mn904 = '';
                $mu904 = '';
            }
            echo '<li class="' . $mu904 . '">';
            TitleBarBlue("INFORMASI Akun", 'mdi mdi-chart-areaspline');
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
        <?php echo '</ul></li>'; }

        MenuLink("ACCLejerList.php", "Pembukaan Akaun", 908, @$_REQUEST['vw']);
        MenuLink("ACCSingleEntryList.php", "Jurnal Entry", 908, @$_REQUEST['vw']);
        MenuLink("ACCGeneralejer.php", "General Ledger", 908, @$_REQUEST['vw']);
        MenuLink("ACCBankrecon.php", "Bank Rekonsilasi", 908, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Online Banking(B2B)", 908, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Hapus Kira", 908, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.akaun.assetTetap
        if (@$mn == 909) {
            $mn909 = "mm-collapse mm-show";
            $mu909 = "mm-active";
        } else {
            $mn909 = '';
            $mu909 = '';
        }
        echo '<li class="' . $mu909 . '">';
        TitleBarBlue("Asset Tetap", 'mdi mdi-database');
        echo '<ul class="sub-menu ' . $mn909 . '" aria-expanded="true">';
        // MenuLink("blank.php", "Senarai Asset", 909, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Penyusutan", 909, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Laporan", 909, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.akaun.stok
        if (@$mn == 910) {
            $mn910 = "mm-collapse mm-show";
            $mu910 = "mm-active";
        } else {
            $mn910 = '';
            $mu910 = '';
        }
        echo '<li class="' . $mu910 . '">';
        TitleBarBlue("Stok", 'mdi mdi-hexagon-slice-5');
        echo '<ul class="sub-menu ' . $mn910 . '" aria-expanded="true">';
        // MenuLink("blank.php", "Senarai Produk", 910, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Pelarasan", 910, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.akaun.bukuTunai
        if (@$mn == 911) {
            $mn911 = "mm-collapse mm-show";
            $mu911 = "mm-active";
        } else {
            $mn911 = '';
            $mu911 = '';
        }
        echo '<li class="' . $mu911 . '">';
        TitleBarBlue("Buku Tunai", 'mdi mdi-book-open-variant');
        echo '<ul class="sub-menu ' . $mn911 . '" aria-expanded="true">';
        MenuLink("ACCvouchersList.php", "Pembayaran", 911, @$_REQUEST['vw']);
        MenuLink("ACCResitList.php", "Terimaan(Resit)", 911, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.akaun.penghutang
        if (@$mn == 912) {
            $mn912 = "mm-collapse mm-show";
            $mu912 = "mm-active";
        } else {
            $mn912 = '';
            $mu912 = '';
        }
        echo '<li class="' . $mu912 . '">';
        TitleBarBlue("Penghutang", 'mdi mdi-wallet');
        echo '<ul class="sub-menu ' . $mn912 . '" aria-expanded="true">';
        MenuLink("ACCQuotationList.php", "Sebut Harga", 912, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Penghantar(DO)", 912, @$_REQUEST['vw']);
        MenuLink("ACCInvoiceList.php", "Invoice", 912, @$_REQUEST['vw']);
        MenuLink("ACCDebtorList.php", "Terima(Invoice)", 912, @$_REQUEST['vw']);
        MenuLink("ACCDebtorBulkList.php", "Terima(Bulk Payments)", 912, @$_REQUEST['vw']);
        MenuLink("ACCcreditNoteList.php", "Nota Kredit", 912, @$_REQUEST['vw']);
            // admin.akaun.penghutang.laporan
            echo '<li class="' . $mu912 . '">';
            TitleBarBlue("Laporan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn912 . '" aria-expanded="true">';
            // MenuLink("blank.php", "Umum", 912, @$_REQUEST['vw']);
            MenuLink("reportDebtor.php", "Aging", 912, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';
        // admin.akaun.pemiutang
        if (@$mn == 913) {
            $mn913 = "mm-collapse mm-show";
            $mu913 = "mm-active";
        } else {
            $mn913 = '';
            $mu913 = '';
        }
        echo '<li class="' . $mu913 . '">';
        TitleBarBlue("Pemiutang", 'mdi mdi-wallet');
        echo '<ul class="sub-menu ' . $mn913 . '" aria-expanded="true">';
        MenuLink("ACCpurchaseList.php", "Belian(Purchase Order)", 913, @$_REQUEST['vw']);
        MenuLink("ACCpurchaseInvoiceList.php", "Belian(Purchase Invoice)", 913, @$_REQUEST['vw']);
        MenuLink("ACCbillList.php", "Bayaran(Baucev PI)", 913, @$_REQUEST['vw']);
        MenuLink("ACCdebitNoteList.php", "Nota Debit", 913, @$_REQUEST['vw']);
            // admin.akaun.pemiutang.laporan
            echo '<li class="' . $mu913 . '">';
            TitleBarBlue("Laporan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn913 . '" aria-expanded="true">';
            // MenuLink("blank.php", "Umum", 913, @$_REQUEST['vw']);
            MenuLink("reportCreditor.php", "Aging", 913, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';
        // admin.akaun.pelaburan
        if (@$mn == 914) {
            $mn914 = "mm-collapse mm-show";
            $mu914 = "mm-active";
        } else {
            $mn914 = '';
            $mu914 = '';
        }
        echo '<li class="' . $mu914 . '">';
        TitleBarBlue("Pelaburan", 'mdi mdi-shape');
        echo '<ul class="sub-menu ' . $mn914 . '" aria-expanded="true">';
        MenuLink("reportCreditor.php", "Daftar Projek", 914, @$_REQUEST['vw']);
        MenuLink("ACCvouchersProjectsList.php", "Bayaran(Baucer)", 914, @$_REQUEST['vw']);
        MenuLink("ACCinvestList.php", "Invoice", 914, @$_REQUEST['vw']);
        MenuLink("ACCInvDebtorList.php", "Terima(Resit)", 914, @$_REQUEST['vw']);
        MenuLink("investorReports.php", "Laporan", 914, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.akaun.simpananBank
        if (@$mn == 915) {
            $mn915 = "mm-collapse mm-show";
            $mu915 = "mm-active";
        } else {
            $mn915 = '';
            $mu915 = '';
        }
        echo '<li class="' . $mu915 . '">';
        TitleBarBlue("Simpanan Bank", 'mdi mdi-bank');
        echo '<ul class="sub-menu ' . $mn915 . '" aria-expanded="true">';
        // MenuLink("blank.php", "Mohon", 915, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Tambahan", 915, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Pindah Wang", 915, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Settlement", 915, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Penyata", 915, @$_REQUEST['vw']);
            // admin.akaun.simpananBank.laporan
            echo '<li class="' . $mu915 . '">';
            TitleBarBlue("Laporan", 'mdi mdi-contacts-outline');
            echo '<ul class="sub-menu ' . $mn915 . '" aria-expanded="true">';
            // MenuLink("blank.php", "Umum", 915, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Transaksi", 915, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';
        // admin.akaun.eInvoice
        if (@$mn == 916) {
            $mn916 = "mm-collapse mm-show";
            $mu916 = "mm-active";
        } else {
            $mn916 = '';
            $mu916 = '';
        }
        echo '<li class="' . $mu916 . '">';
        TitleBarBlue("E-Invoice(LHDN)", 'mdi mdi-script');
        echo '<ul class="sub-menu ' . $mn916 . '" aria-expanded="true">';
        // MenuLink("blank.php", "Self Billed Document", 916, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Bulk E-Invoice", 916, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.akaun.pengurusanKewangan
        if (@$mn == 917) {
            $mn917 = "mm-collapse mm-show";
            $mu917 = "mm-active";
        } else {
            $mn917 = '';
            $mu917 = '';
        }
        echo '<li class="' . $mu917 . '">';
        TitleBarBlue("Pengurusan Kewangan", 'mdi mdi-cash-usd');
        echo '<ul class="sub-menu ' . $mn917 . '" aria-expanded="true">';
        // MenuLink("blank.php", "Budget", 917, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Pecahan Keuntungan(KPWK)", 917, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Laporan KPWK", 917, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.akaun.consolidate
        if (@$mn == 918) {
            $mn918 = "mm-collapse mm-show";
            $mu918 = "mm-active";
        } else {
            $mn918 = '';
            $mu918 = '';
        }
        echo '<li class="' . $mu918 . '">';
        TitleBarBlue("Consolidate", 'mdi mdi-consolidate');
        echo '<ul class="sub-menu ' . $mn918 . '" aria-expanded="true">';
        // MenuLink("blank.php", "Anak Syarikat", 918, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Process Consolidate", 918, @$_REQUEST['vw']);
            // admin.akaun.consolidate.laporan
            echo '<li class="' . $mu918 . '">';
            TitleBarBlue("Laporan", 'mdi mdi-contacts-outline');
            echo '<ul class="sub-menu ' . $mn918 . '" aria-expanded="true">';
            echo '</ul></li>';
        echo '</ul></li>';
        // admin.akaun.sumberManusia
        if (@$mn == 919) {
            $mn919 = "mm-collapse mm-show";
            $mu919 = "mm-active";
        } else {
            $mn919 = '';
            $mu919 = '';
        }
        echo '<li class="' . $mu919 . '">';
        TitleBarBlue("Sumber Manusia", 'mdi mdi-account-group');
        echo '<ul class="sub-menu ' . $mn919 . '" aria-expanded="true">';
        MenuLink("staff.php", "Senarai Staf", 919, @$_REQUEST['vw']);
        MenuLink("leave.php", "Permohonan Cuti", 919, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Gaji", 919, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Kiraan Bonus", 919, @$_REQUEST['vw']);
        // MenuLink("blank.php", "EPF", 919, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Socso", 919, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.akaun.dividen
        if (@$mn == 920) {
            $mn920 = "mm-collapse mm-show";
            $mu920 = "mm-active";
        } else {
            $mn920 = '';
            $mu920 = '';
        }
        echo '<li class="' . $mu920 . '">';
        TitleBarBlue("Dividen", 'mdi mdi-wallet-giftcard');
        echo '<ul class="sub-menu ' . $mn920 . '" aria-expanded="true">';
        MenuLink("dividenPeratusBlnKhd.php", "Kiraan", 920, @$_REQUEST['vw']);
        MenuLink("reportsDIV.php", "Laporan", 920, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.akaun.laporanKewangan
        if (@$mn == 921) {
            $mn921 = "mm-collapse mm-show";
            $mu921 = "mm-active";
        } else {
            $mn921 = '';
            $mu921 = '';
        }
        echo '<li class="' . $mu921 . '">';
        TitleBarBlue("Laporan Kewangan", 'mdi mdi-cash');
        echo '<ul class="sub-menu ' . $mn921 . '" aria-expanded="true">';
        MenuLink("reports?cat=D.php", "Umum", 921, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Lain-lain", 921, @$_REQUEST['vw']);
        echo '</ul></li>';


        // admin.pengurusan
        SubTitleBar("PENGURUSAN");
        // admin.pengurusan.surat/email
        if (@$mn == 922) {
            $mn922 = "mm-collapse mm-show";
            $mu922 = "mm-active";
        } else {
            $mn922 = '';
            $mu922 = '';
        }
        echo '<li class="' . $mu922 . '">';
        TitleBarBlue("Surat/Email", 'mdi mdi-email');
        echo '<ul class="sub-menu ' . $mn922 . '" aria-expanded="true">';
        MenuLink("memberLetter.php?page=add&group=&code=", "Kandungan Surat", 922, @$_REQUEST['vw']);
        MenuLink("memberLetter.php", "Senarai Surat", 922, @$_REQUEST['vw']);
        // MenuLink("blank.php", "SMS/Whatsapp/Notice", 922, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.pengurusan.alk/agm
        if (@$mn == 923) {
            $mn923 = "mm-collapse mm-show";
            $mu923 = "mm-active";
        } else {
            $mn923 = '';
            $mu923 = '';
        }
        echo '<li class="' . $mu923 . '">';
        TitleBarBlue("ALK/AGM", 'mdi mdi-file-document');
        echo '<ul class="sub-menu ' . $mn923 . '" aria-expanded="true">';
        MenuLink("minit.php", "Senarai Minit Mesyuarat", 923, @$_REQUEST['vw']);
        MenuLink("agm.php", "Senarai Dokumen AGM", 923, @$_REQUEST['vw']);
        // MenuLink("blank.php", "E-Voting(AGM)", 923, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Kehadiran", 923, @$_REQUEST['vw']);
            // admin.pengurusan.alk
            echo '<li class="' . $mu923 . '">';
            TitleBarBlue("Laporan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn923 . '" aria-expanded="true">';
            // MenuLink("blank.php", "Umum", 923, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Keharidan AGM", 923, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';
        
        // admin.takaful/tabung
        SubTitleBar("ASURANSI & TABUNGAN");
        // admin.takaful/tabung.takaful
        if (@$mn == 924) {
            $mn924 = "mm-collapse mm-show";
            $mu924 = "mm-active";
        } else {
            $mn924 = '';
            $mu924 = '';
        }
        echo '<li class="' . $mu924 . '">';
        TitleBarBlue("Asuransi", 'mdi mdi-wallet-travel');
        echo '<ul class="sub-menu ' . $mn924 . '" aria-expanded="true">';
            // motor
            echo '<li class="' . $mu924 . '">';
            TitleBarBlue("Motor", 'mdi mdi-wallet-travel');
            echo '<ul class="sub-menu ' . $mn924 . '" aria-expanded="true">';
            MenuLink("insuranApply.php", "Jualan", 924, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Bayaran", 924, @$_REQUEST['vw']);
            MenuLink("insuranList.php", "Senarai Takaful", 924, @$_REQUEST['vw']);
            MenuLink("insuranListNotActive.php", "Senarai Tamat", 924, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Laporan", 924, @$_REQUEST['vw']);
            echo '</ul></li>';
            // perlindungan
            echo '<li class="' . $mu924 . '">';
            TitleBarBlue("Perlindungan", 'mdi mdi-wallet-travel');
            echo '<ul class="sub-menu ' . $mn924 . '" aria-expanded="true">';
            // MenuLink("blank.php", "Mohon Baru", 924, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Senarai Lulus", 924, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Potongan", 924, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Tuntutan", 924, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Laporan", 924, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';
        // admin.takaful/tabung.tabung
        if (@$mn == 925) {
            $mn925 = "mm-collapse mm-show";
            $mu925 = "mm-active";
        } else {
            $mn925 = '';
            $mu925 = '';
        }
        echo '<li class="' . $mu925 . '">';
        TitleBarBlue("Tabung", 'mdi mdi-wallet');
        echo '<ul class="sub-menu ' . $mn925 . '" aria-expanded="true">';
        // MenuLink("blank.php", "Senarai Khairat", 925, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Kecemasan", 925, @$_REQUEST['vw']);
        echo '</ul></li>';


        // admin.import/export
        SubTitleBar("IMPORT/EXPORT");
        // admin.import/export.importFile
        if (@$mn == 926) {
            $mn926 = "mm-collapse mm-show";
            $mu926 = "mm-active";
        } else {
            $mn926 = '';
            $mu926 = '';
        }
        echo '<li class="' . $mu926 . '">';
        TitleBarBlue("Import File", 'mdi mdi-file-cog-outline');
        echo '<ul class="sub-menu ' . $mn926 . '" aria-expanded="true">';
        // MenuLink("blank.php", "File Potongan(Angkasa)", 926, @$_REQUEST['vw']);
        // MenuLink("blank.php", "File Potongan(Majikan)", 926, @$_REQUEST['vw']);
        // MenuLink("blank.php", "File Wajib & Syer", 926, @$_REQUEST['vw']);
        // MenuLink("blank.php", "File Pembiayaan", 926, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.import/export.export
        if (@$mn == 927) {
            $mn927 = "mm-collapse mm-show";
            $mu927 = "mm-active";
        } else {
            $mn927 = '';
            $mu927 = '';
        }
        echo '<li class="' . $mu927 . '">';
        TitleBarBlue("Export File", 'mdi mdi-file-cog-outline');
        echo '<ul class="sub-menu ' . $mn927 . '" aria-expanded="true">';
        // MenuLink("blank.php", "File Potongan(Export)", 927, @$_REQUEST['vw']);
            // admin.import/export.export.laporan
            echo '<li class="' . $mu927 . '">';
            TitleBarBlue("Laporan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn927 . '" aria-expanded="true">';
            echo '</ul></li>';
        echo '</ul></li>';


        // admin.tatapan
        SubTitleBar("TATAPAN");
        // admin.tatapan.anggota
        if (@$mn == 928) {
            $mn928 = "mm-collapse mm-show";
            $mu928 = "mm-active";
        } else {
            $mn928 = '';
            $mu928 = '';
        }
        echo '<li class="' . $mu928 . '">';
        TitleBarBlue("Anggota", 'mdi mdi-home');
        echo '<ul class="sub-menu ' . $mn928 . '" aria-expanded="true">';
        // MenuLink("blank.php", "Laman Utama", 928, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Buletin", 928, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Kelayakan Anggota", 928, @$_REQUEST['vw']);
        // MenuLink("blank.php", "Info Pembayaran", 928, @$_REQUEST['vw']);
        echo '</ul></li>';
        // admin.tatapan.admin
        if (@$mn == 929) {
            $mn929 = "mm-collapse mm-show";
            $mu929 = "mm-active";
        } else {
            $mn929 = '';
            $mu929 = '';
        }
        echo '<li class="' . $mu929 . '">';
        TitleBarBlue("Admin", 'mdi mdi-home');
        echo '<ul class="sub-menu ' . $mn929 . '" aria-expanded="true">';


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

        


            // admin.tatapan.admin.api
            echo '<li class="' . $mu929 . '">';
            TitleBarBlue("API", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn929 . '" aria-expanded="true">';
            // MenuLink("blank.php", "General", 929, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Simpanan", 929, @$_REQUEST['vw']);
            // MenuLink("blank.php", "Pembiayaan", 929, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';

        // admin.simpanan
        // if (@$mn == 930) {
        //     $mn930 = "mm-collapse mm-show";
        //     $mu930 = "mm-active";
        // } else {
        //     $mn930 = '';
        //     $mu930 = '';
        // }
        // echo '<li class="' . $mu930 . '">';
        // TitleBarBlue("SIMPANAN", 'mdi mdi-bank');
        // echo '<ul class="sub-menu ' . $mn930 . '" aria-expanded="false">';
        // MenuLink("loansimpanan1.php", "Rekening Simpanan", 930, @$_REQUEST['vw']);
        // MenuLink("loanApplysimpanan.php", "Entry Data Simpanan", 930, @$_REQUEST['vw']);
        // echo '<li>';
        // TitleBarBlue("Laporan", 'mdi mdi-file');
        // echo '<ul class="sub-menu ' . $mn930 . '" aria-expanded="true">';
        // MenuLink("reportsimpanan.php", "Simpanan", 930, @$_REQUEST['vw']);
        // echo '</ul></li>';
        // echo '</ul></li>';


        

        // echo '<li>';
        // TitleBarBlue("Laporan", 'mdi mdi-file');
        // echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
        // // MenuLink("blank.php", "Keseluruhan Wajib & Syer", 905, @$_REQUEST['vw']);
        // echo '</ul></li>';
        
        

        // if (@$mn == 900) {
        //     $mn900 = "mm-collapse mm-show";
        //     $mu900 = "mm-active";
        // } else {
        //     $mn900 = '';
        //     $mu900 = '';
        // }
        // echo '<li class="' . $mu900 . '">';
        // TitleBarBlue("DASHBOARD", 'mdi mdi-chart-pie');
        // echo '<ul class="sub-menu ' . $mn900 . '" aria-expanded="false">';
        // MenuLink("dashboard.php", "Dashboard Koperasi", 900, @$_REQUEST['vw']);
        // echo '</ul></li>';        






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
function SubTitleBar($strTitle) {
    print '<li>
        <small style="color:grey; padding:13px 25px; display: block; font-weight:bold; letter-spacing:0.1rem">
            ' . $strTitle . '
        </small>
    </li>';
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