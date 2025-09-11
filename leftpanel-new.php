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
        echo '<li class="' . $mu905 . '">';
        TitleBarBlue("DASHBOARD", 'mdi mdi-chart-pie');
        echo '<ul class="sub-menu ' . $mn905 . '" aria-expanded="false">';
        MenuLink("blank.php", "Panduan Pengguna", 905, @$_REQUEST['vw']);
        echo '</ul></li>';

        // admin.anggota
        echo '<li class="' . $mu905 . '">';
        TitleBarBlue("ANGGOTA", 'mdi mdi-contacts-outline');
        echo '<ul class="sub-menu ' . $mn905 . '" aria-expanded="false">';
            // admin.anggota.infoAnggota
            echo '<li>';
            TitleBarBlue("Info Anggota", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("member.php", "Pengajuan", 905, @$_REQUEST['vw']);
            MenuLink("memberProfil.php", "Profil", 905, @$_REQUEST['vw']);
            MenuLink("memberT.php", "Berhenti", 905, @$_REQUEST['vw']);
                // admin.anggota.infoAnggota.laporan
                echo '<li>';
                TitleBarBlue("Laporan", 'mdi mdi-file');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("reports.php?cat=A", "Umum", 905, @$_REQUEST['vw']);
                MenuLink("rAllFeesShare.php", "Keseluruhan Yuran & Syer", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
            echo '</ul></li>';
            // admin.anggota.modalSimpanan
            echo '<li>';
            TitleBarBlue("Modal / Simpanan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("memberStmtA.php", "Info Modal", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Info Simpanan", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Jurnal Pindahan", 905, @$_REQUEST['vw']);
                // admin.anggota.modalSimpanan.pengeluaran
                echo '<li>';
                TitleBarBlue("Pengeluaran", 'mdi mdi-file');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("blank.php", "Permohonan", 905, @$_REQUEST['vw']);
                MenuLink("vouchersList.php", "Baucer", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
                // admin.anggota.modalSimpanan.penambahan
                echo '<li>';
                TitleBarBlue("Penambahan", 'mdi mdi-file');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("blank.php", "Permohonan", 905, @$_REQUEST['vw']);
                MenuLink("resitList.php", "Resit", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
            MenuLink("blank.php", "Transaksi Online", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Proses Hibah Simpanan", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Penyata Simpanan", 905, @$_REQUEST['vw']);
                // admin.anggota.modalSimpanan.laporan
                echo '<li>';
                TitleBarBlue("Laporan", 'mdi mdi-file');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("blank.php", "Asset (Pokok & Wajib)", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Simpanan", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Transaksi", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
            echo '</ul></li>';
            // admin anggota.simpananBerjangka
            echo '<li>';
            TitleBarBlue("Simpanan Berjangka (FD)", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Info Akaun Simpanan", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Jurnal Pindahan", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Pengeluaran(Baucer)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Penambahan(Resit)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Proses Hibah", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Transaksi Online", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Penyata (Cert)", 905, @$_REQUEST['vw']);
                // admin.anggota.simpananBerjangka.laporan
                echo '<li>';
                TitleBarBlue("Laporan", 'mdi mdi-file');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("blank.php", "Simpanan Berjangka", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Transaksi", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
            echo '</ul></li>';
        echo '</ul></li>';

        // admin.pembiayaan
        echo '<li class="' . $mu905 . '">';
        TitleBarBlue("PEMBIAYAAN", 'mdi mdi-account-cash-outline');
        echo '<ul class="sub-menu ' . $mn905 . '" aria-expanded="false">';
            // admin.pembiayaan.infoPembiayaan
            echo '<li>';
            TitleBarBlue("Info Pembiayaan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Daftar Pengajuan", 905, @$_REQUEST['vw']);
                // admin.pembiayaan.infoPembiayaan.advancePayment
                echo '<li>';
                TitleBarBlue("Mohon (Advance Payment)", 'mdi mdi-file');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("blank.php", "Daftar Pengajuan", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Lulusan", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
                MenuLink("blank.php", "Senarai (Diluluskan)", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Belian Sijil Komoditi", 905, @$_REQUEST['vw']);
                MenuLink("journalsList.php", "Jurnal Pengeluaran", 905, @$_REQUEST['vw']);
                MenuLink("resit.php?jenis=2", "Terima Pembayaran", 905, @$_REQUEST['vw']);
                MenuLink("loanTable.php", "Senarai (Selesai)", 905, @$_REQUEST['vw']);
                MenuLink("memberPotonganAll.php", "Potongan Gaji", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.pembiayaan.laporan
            echo '<li>';
            TitleBarBlue("Laporan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("reports.php?cat=B", "Umum", 905, @$_REQUEST['vw']);
            MenuLink("rloanApproved.php", "Kelulusan", 905, @$_REQUEST['vw']);
            MenuLink("rptAgingLoan.php", "Aging", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';

        // admin.hutangLapuk
        echo '<li>';
        TitleBarBlue("Hutang Lapuk", 'mdi mdi-credit-card-outline');
        echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            // admin.hutangLapuk.infoNPFPembiayaan
            echo '<li>';
            TitleBarBlue("Info NPF Pembiayaan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Profil", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Senarai Pembiayaan", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Terima Pembiayaan (Resit)", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.hutangLapuk.Laporan
            echo '<li>';
            TitleBarBlue("Laporan", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Umum", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "> 12", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "< 12", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';
        
        
        // admin.akaun
        echo '<li>';
        TitleBarBlue("Akaun(Keuangan)", 'mdi mdi-wallet-outline');
        echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            // admin.akaun.master
            echo '<li>';
            TitleBarBlue("Master", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                // admin.akaun.master.tatapanAkaun
                echo '<li>';
                TitleBarBlue("Tatapan Akaun", 'mdi mdi-file');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("blank.php", "Kod Object/Akaun", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Jenis Pembiayaan", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
            MenuLink("ACCLejerList.php", "Pembukaan Akaun", 905, @$_REQUEST['vw']);
            MenuLink("ACCSingleEntryList.php", "Jurnal Entry", 905, @$_REQUEST['vw']);
            MenuLink("ACCGeneralejer.php", "General Ledger", 905, @$_REQUEST['vw']);
            MenuLink("ACCBankrecon.php", "Bank Rekonsilasi", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Online Banking(B2B)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Hapus Kira", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.akaun.assetTetap
            echo '<li>';
            TitleBarBlue("Asset Tetap", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Senarai Asset", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Penyusutan", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Laporan", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.akaun.stok
            echo '<li>';
            TitleBarBlue("Stok", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Senarai Produk", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Pelarasan", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.akaun.bukuTunai
            echo '<li>';
            TitleBarBlue("Buku Tunai", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("ACCvouchersList.php", "Pembayaran", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Terima (Resit)", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.akaun.penghutang
            echo '<li>';
            TitleBarBlue("Penghutang(Dabtors)", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Sebut Harga", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Penghantar(DO)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Invoice", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Terima(Invoice)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Terima(Bulk Payments)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Nota Kredit", 905, @$_REQUEST['vw']);
                // admin.akaun.penghutang.laporan
                echo '<li>';
                TitleBarBlue("Laporan", 'mdi mdi-contacts-outline');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("blank.php", "Umum", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Aging", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
            echo '</ul></li>';
            // admin.akaun.pemiutang
            echo '<li>';
            TitleBarBlue("Pemiutang(Creditors)", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Belian(Purchase Order)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Belian(Purchase Invoice)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Bayaran(Baucev PI)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Nota Debit", 905, @$_REQUEST['vw']);
                // admin.akaun.pemiutang.laporan
                echo '<li>';
                TitleBarBlue("Laporan", 'mdi mdi-contacts-outline');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("blank.php", "Umum", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Aging", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
            echo '</ul></li>';
            // admin.akaun.pelaburan
            echo '<li>';
            TitleBarBlue("Pelaburan", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Daftar Projek", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Bayaran(Baucer)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Invoice", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Terima(Resit)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Laporan", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.akaun.simpananBank
            echo '<li>';
            TitleBarBlue("Simpanan Bank", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Mohon", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Tambahan", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Pindah Wang", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Settlement", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Penyata", 905, @$_REQUEST['vw']);
                // admin.akaun.simpananBank.laporan
                echo '<li>';
                TitleBarBlue("Laporan", 'mdi mdi-contacts-outline');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("blank.php", "Umum", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Transaksi", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
            echo '</ul></li>';
            // admin.akaun.eInvoice
            echo '<li>';
            TitleBarBlue("E-Invoice(LHDN)", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Self Billed Document", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Bulk E-Invoice", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.akaun.pengurusanKewangan
            echo '<li>';
            TitleBarBlue("Pengurusan Kewangan", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Budget", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Pecahan Keuntungan(KPWK)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Laporan KPWK", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.akaun.consolidate
            echo '<li>';
            TitleBarBlue("Consolidate", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Anak Syarikat", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Process Consolidate", 905, @$_REQUEST['vw']);
                // admin.akaun.consolidate.laporan
                echo '<li>';
                TitleBarBlue("Consolidate", 'mdi mdi-contacts-outline');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                echo '</ul></li>';
            echo '</ul></li>';
            // admin.akaun.sumberManusia
            echo '<li>';
            TitleBarBlue("Sumber Manusia", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Senarai Staf", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Permohonan Cuti", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Gaji", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Kiraan Bonus", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "EPF", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Socso", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.akaun.dividen
            echo '<li>';
            TitleBarBlue("Dividen", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Kiraan", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Laporan", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.akaun.laporanKewangan
            echo '<li>';
            TitleBarBlue("Laporan Kewangan", 'mdi mdi-wallet-outline');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Umum", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Lain-lain", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';


        // admin.pengurusan
        echo '<li>';
        TitleBarBlue("Pengurusan", 'mdi mdi-note-outline');
        echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            // admin.pengurusan.surat/email
            echo '<li>';
            TitleBarBlue("Surat/Email", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Kandungan Surat", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Senarai Surat", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "SMS/Whatsapp/Notice", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.pengurusan.alk/agm
            echo '<li>';
            TitleBarBlue("ALK/AGM", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Senarai Minit Mesyuarat", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Senarai Dokumen AGM", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "E-Voting(AGM)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Kehadiran", 905, @$_REQUEST['vw']);
                // admin.pengurusan.alk
                echo '<li>';
                TitleBarBlue("Laporan", 'mdi mdi-file');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("blank.php", "Umum", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Keharidan AGM", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
            echo '</ul></li>';
        echo '</ul></li>';
        
        // admin.takaful/tabung
        echo '<li>';
        TitleBarBlue("TAKAFUL/TABUNG", 'mdi mdi-wallet-outline');
        echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            // admin.takaful/tabung.takaful
            echo '<li>';
            TitleBarBlue("Takaful", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Motor", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Jualan", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Perlindungan", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.takaful/tabung.tabung
            echo '<li>';
            TitleBarBlue("Tabung", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Senarai Khairat", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Kecemasan", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
        echo '</ul></li>';


        // admin.import/export
        echo '<li>';
        TitleBarBlue("IMPORT/EXPORT", 'mdi mdi-file-cog-outline');
        echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            // admin.import/export.importFile
            echo '<li>';
            TitleBarBlue("Import File", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "File Potongan(Angkasa)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "File Potongan(Majikan)", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "File Yuran & Syer", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "File Pembiayaan", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.import/export.export
            echo '<li>';
            TitleBarBlue("Export File", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "File Potongan(Export)", 905, @$_REQUEST['vw']);
                // admin.import/export.export.laporan
                echo '<li>';
                TitleBarBlue("Laporan", 'mdi mdi-file');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                echo '</ul></li>';
            echo '</ul></li>';
        echo '</ul></li>';


        // admin.tatapan
        echo '<li>';
        TitleBarBlue("TATAPAN", 'mdi mdi-home');
        echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            // admin.tatapan.anggota
            echo '<li>';
            TitleBarBlue("Anggota", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Laman Utama", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Buletin", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Kelayakan Anggota", 905, @$_REQUEST['vw']);
            MenuLink("blank.php", "Info Pembayaran", 905, @$_REQUEST['vw']);
            echo '</ul></li>';
            // admin.tatapan.admin
            echo '<li>';
            TitleBarBlue("Admin", 'mdi mdi-file');
            echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
            MenuLink("blank.php", "Informasi Asas", 905, @$_REQUEST['vw']);
                // admin.tatapan.admin.api
                echo '<li>';
                TitleBarBlue("API", 'mdi mdi-file');
                echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
                MenuLink("blank.php", "General", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Simpanan", 905, @$_REQUEST['vw']);
                MenuLink("blank.php", "Pembiayaan", 905, @$_REQUEST['vw']);
                echo '</ul></li>';
            echo '</ul></li>';
        echo '</ul></li>';

        // admin.simpanan
        echo '<li class="' . $mu946 . '">';
        TitleBarBlue("SIMPANAN", 'mdi mdi-bank');
        echo '<ul class="sub-menu ' . $mn946 . '" aria-expanded="false">';
        MenuLink("loansimpanan1.php", "Rekening Simpanan", 946, @$_REQUEST['vw']);
        MenuLink("loanApplysimpanan.php", "Entry Data Simpanan", 946, @$_REQUEST['vw']);
        echo '<li>';
        TitleBarBlue("Laporan", 'mdi mdi-file');
        echo '<ul class="sub-menu ' . $mn946 . '" aria-expanded="true">';
        MenuLink("reportsimpanan.php", "Simpanan", 946, @$_REQUEST['vw']);
        echo '</ul></li>';
        echo '</ul></li>';


        

        // echo '<li>';
        // TitleBarBlue("Laporan", 'mdi mdi-file');
        // echo '<ul class="sub-menu ' . $mn906 . '" aria-expanded="true">';
        // MenuLink("blank.php", "Keseluruhan Yuran & Syer", 905, @$_REQUEST['vw']);
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