<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	reports.php
 *          Date 		: 	29/03/2004
 *********************************************************************************/
include("header.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	$temp = '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
	print $temp;
}

$sFileName = "?vw=reports&mn=$mn";
$sFileRef  = "?vw=reports&mn=$mn";
$title     = $lapList[array_search($cat, $lapVal)];

?>
<h5 class="card-title"><? print strtoupper($title); ?></h5>
<div style="width: 100%; text-align:left">
	<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
		<? if ($cat == 'A') { ?>
			<div>&nbsp;</div>
			<tr>
				<td class="Label" valign="top" colspan="3">
					<h6 class="card-subtitle"><u><b>ANGGOTA</b></u></h6>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA1')">Permohonan Menjadi Anggota</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA2')">Kelulusan Anggota</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA3')">Senarai Permohonan Ditolak</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA14')">Senarai Daftar Anggota</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectUrusniaga('rptA26')">Senarai Daftar Anggota Berdasarkan Tahun</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptPembiayaanT')">Senarai Anggota Yang Mempunyai Loan / Tiada Loan</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptJumPotonganThn')">Senarai Potongan Pembiayaan / Wajib Bagi Tahunan </a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA4')">Senarai Anggota Masih Berkhidmat</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA8')">Senarai Anggota Ada Emel</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA9')">Senarai Anggota Tiada Emel</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA10')">Senarai Anggota Ada Penama</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA11')">Senarai Anggota Tiada Penama</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA19')">Senarai Anggota Ada Maklumat Bank</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA20')">Senarai Anggota Tiada Maklumat Bank</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA12a')">Senarai Permohonan Berhenti Anggota</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA12')">Senarai Permohonan Anggota Berhenti Diluluskan</a></li>
					<li id="print" class="textFont"><a href="#" onclick="selectAnggota('rptA13')">Senarai Permohonan Anggota Berhenti Ditolak</a></li>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td class="Label" valign="top" colspan="3">
					<h6 class="card-subtitle"><u><b>PENGURUSAN</b></u></h6>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPengurusan('rptF1')">Ringkasan Keseluruhan Anggota Mengikut Jabatan/Cawangan</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPengurusan('rptF2')">Ringkasan Keseluruhan Anggota Mengikut Jantina</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPengurusan('rptF3')">Ringkasan Keseluruhan Anggota Mengikut Bangsa</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPengurusan('rptF4')">Ringkasan Keseluruhan Anggota Mengikut Agama</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPengurusan('rptF5')">Ringkasan Keseluruhan Anggota Mengikut Skala Umur</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPengurusan('rptF6')">Ringkasan Keseluruhan Anggota Mengikut Skala Gaji</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPengurusan('rptF7')">Ringkasan Keseluruhan Anggota Mengikut Negeri</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPengurusan('rptAllFeesC')">Ringkasan Keseluruhan Wajib Anggota Mengikut Pegangan Wajib</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPengurusan('rptAllFeesKhas')">Ringkasan Keseluruhan Simpanan Anggota Mengikut Pegangan Simpanan Khas (Deposit)</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPengurusan('rptAllFeesSHM')">Ringkasan Keseluruhan Saham Anggota Mengikut Nombor Keanggotaan</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectAnggota('rptmbrSahBaki')">Senarai Baki (Wajib & Pokok) Terkumpul Berdasarkan Tanggal Pilihan</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptPecahanPinWajib')">Ringkasan Baki Akhir & Baki Awal Bagi Tahun</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptPecahanPin')">Jumlah Dan Pecahan Pinjaman Yang Dikeluarkan</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptBakiAwlAkhir')">Jumlah Pecahan Baki Awal dan Baki Akhir Pembiayaan Bagi Tahun</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptSenaraiBakiAwlAkhir')">Senarai Baki Akhir Keanggotaan Bagi Tahun</a></li>
					<!--<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptSenaraiBakiAkhirPem')">//Ringkasan  Baki Anggota Pembiayaan Peribadi</a></li>-->
				</td>
			</tr>
			<? if (get_session("Cookie_groupID") == 2) { ?>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="Label" valign="top" colspan="3">
						<h6 class="card-subtitle"><u><b>INFORPASI ASAS</b></u></h6>
						<?
						for ($i = 0; $i < count($basicList); $i++) {
							print '	<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectAsas(\'' . $basicVal[$i] . '\')">' . $basicList[$i] . '</a></li>';
						}
						?>
					</td>
				</tr>
			<? } ?>





		<? } elseif ($cat == 'B') { ?>

			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td class="Label" valign="top">
					<h6 class="card-subtitle"><u>PERPOHONAN</u></h6>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB1')">Permohonan Pembiayaan</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB2')">Kelulusan Pembiayaan</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB3')">Pembatalan Pembiayaan</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB2A')">Keseluruhan Pembiayaan</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptkomoditi')">Laporan Komoditi</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptkomoditisah')">Laporan Pengesahan Komoditi</a>
			</tr>

			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td class="Label" valign="top">
					<h6 class="card-subtitle"><u>SENARAI TERIMA PROSES</u></h6>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectBiaya('F')">Permohonan Pembiayaan Yang Diterima dan Diproses Bagi Jenis Pembiayaan Pembiayaan Peribadi</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectBiaya('rptBiayaTerima')">Pembiayaan Yang Diterima dan Diproses</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectBiaya('A')">Senarai Surat Tawaran Keluar Bagi Jenis Pembiayaan Peribadi</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectBiaya('rptBiayaBond')">Senarai Pembiayaan Bulanan Yang Dikeluarkan</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectBiaya('rptBiayaJangkaan')">Kesimpulan Jangkaan Kutipan Penghutang</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectBiaya('rptBiayaPecahan')">Jumlah dan Pecahan Pinjaman Bulanan</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectBiaya('rptBiayaBAK_AKT1')">Senarai Keseluruhan Baki Akhir Pembiayaan Aktif</a>

				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td class="Label" valign="top">
					<h6 class="card-subtitle"><u>EMEL KOPERASI</u></h6>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptEmel')">Senarai Emel Dihantar</a></li>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td class="Label" valign="top">
					<h6 class="card-subtitle"><u>PENYATA</u></h6>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptPenyataBayaran')">Penyata Kesimpulan Bayaran</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptCodeAcc')">Penyata Laporan Urusniaga</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptbank_urusniaga')">Penyata Laporan Urusniaga Bank</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptbank_resit')">Penyata Laporan Resit</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptbank_baucer')">Penyata Laporan Baucer</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptbank_yuran')">Penyata Laporan Laporan Wajib</a></li>
			</tr>

			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td class="Label" valign="top">
					<h6 class="card-subtitle"><u>DSR</u></h6>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB1')">Permohonan Pembiayaan DSR</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB2D')">Kelulusan Pembiayaan DSR</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB3')">Pembatalan Pembiayaan DSR</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB4')">Laporan Nisbah Pembayaran Balik Hutang (DSR) </a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB2D')">Kelulusan Pembiayaan DSR (ALL)</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR3K')">Kelulusan Pembiayaan DSR (0-3000)</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR3K40')">Kelulusan Pembiayaan DSR (0-3000) (&lt;=40%)</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR3K41')">Kelulusan Pembiayaan DSR (0-3000) (&gt;40%)</a>

					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR5K')">Kelulusan Pembiayaan DSR (3001-5000)</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR5K40')">Kelulusan Pembiayaan DSR (3001-5000) (&lt;=40%)</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR5K41')">Kelulusan Pembiayaan DSR (3001-5000) (&gt;40%)</a>

					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR10K')">Kelulusan Pembiayaan DSR (5001-10000)</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR10K40')">Kelulusan Pembiayaan DSR (5001-10000) (&lt;=40%)</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR10K41')">Kelulusan Pembiayaan DSR (5001-10000) (&gt;40%)</a>

					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR11K')">Kelulusan Pembiayaan DSR (10001 -)</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR11K40')">Kelulusan Pembiayaan DSR (10001 -) (&lt;=40%)</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptDSR11K41')">Kelulusan Pembiayaan DSR (10001 -) (&gt;40%)</a>
			</tr>

		<? } elseif ($cat == 'D') { ?>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td class="Label" valign="top">
					<h6 class="card-subtitle"><u>LAPORAN UTAMA</u></h6>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptA25style')">Laporan Imbangan Duga (Trial Balance)</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptA18')">Laporan Transaksi Penyata Ledger (Trial Balance Detailed)</a>
						<!-- <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptCashFlow')">Laporan Aliran Tunai (Cash Flow)</a> -->
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptACCPNL2')">Laporan Profit And Loss</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptACCBS2')">Laporan Balance Sheet</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptA23')">Laporan Keseluruhan Penyata Ledger</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaanAD('rptA22')">Laporan Penyata Ledger Mengikut Carta Akaun</a>
			</tr>

			<tr>
				<td colspan="3">
					<hr size=1>
				</td>
			</tr>

			<tr>
				<td class="Label" valign="top">
					<h6 class="card-subtitle"><u>LAPORAN LAIN</u></h6>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectProject('rptACCPNL2')">Laporan Profit And Loss Berdasarkan Projek</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectDept('rptACCPNL2')">Laporan Profit And Loss Berdasarkan Jabatan</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectProject('rptACCBS2')">Laporan Balance Sheet Berdasarkan Projek</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectDept('rptACCBS2')">Laporan Balance Sheet Berdasarkan Jabatan</a>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectBankSah('rptACCbank_recon')">Laporan Transaksi Rekonsilasi Bank Ada Pengesahan</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectBankTakSah('rptACCbank_recon')">Laporan Transaksi Rekonsilasi Bank Tiada Pengesahan</a></li>
					<!-- <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectBankSah('rptACCbank_recon2')">Laporan Transaksi Rekonsilasi Bank Ada Pengesahan TRY FORPAT BARU</a></li> -->
					<!-- <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectBankTakSah('rptACCbank_recon2')">Laporan Transaksi Rekonsilasi Bank Tiada Pengesahan TRY FORPAT BARU</a></li> -->
					<!-- <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptACCcashFlow')">Laporan Cash Flow Template SKM</a></li> -->
			</tr>

			<tr>
				<td colspan="3">
					<hr size=1>
				</td>
			</tr>

			<tr>
				<td class="Label" valign="top">
					<h6 class="card-subtitle"><u>PENYATA URUSNIAGA (BUKU TUNAI)</u></h6>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptACCbank_resit')">Laporan Transaksi Resit</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptACCbank_baucer')">Laporan Transaksi Baucer</a></li>
					<!-- <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptACCbank_recon')">Laporan Transaksi Rekonsilasi Bank</a></li> -->
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptACCbank_reconSah')">Laporan Rekonsilasi Bank (Ada Pengesahan)</a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptACCbank_reconTSah')">Laporan Rekonsilasi Bank (Tiada Pengesahan)</a></li>
				</td>
			</tr>

			<tr>
				<td colspan="3">
					<hr size=1>
				</td>
			</tr>

			<tr>
				<td class="Label" valign="top">
					<h6 class="card-subtitle"><u>PENYATA URUSNIAGA (FPX)</u></h6>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptbank_onlineA')">Penyata Laporan Harian Transaksi Atas Talian </a></li>
					<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptbank_onlineX')">Penyata Laporan Harian Transaksi Atas Talian (Tiada Transaksi)</a></li>
				</td>
			</tr>

			</tr>

		<? } ?>

	</table>
	<?php
	include("footer.php");
	print '
<script>
	function selectDividen(rpt) {
		url = "selYear.php?rpt="+rpt+"&id=ALL";
		window.open(url ,"pop","top=100,left=100,width=500,height=300,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}
	
	function selectAsas(code) {
		window.open("rptAsas.php?code="+code ,"pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");		
	}	  

	function selectAnggota(rpt) {
		if (rpt == "rptA4" || rpt == "rptA5" || rpt == "rptA6" || rpt == "rptA7" || rpt == "rptA8" || rpt == "rptA9" ||
			rpt == "rptA10" || rpt == "rptA11" || rpt == "rptA12a" || rpt == "rptA12" || rpt == "rptA13" || rpt == "rptA14" || rpt == "rptA15" ||
			rpt == "rptmbrBersara" ||rpt == "rptA19" ||rpt == "rptA20" || rpt == "rptDaftarAng" || rpt == "rptPembiayaanT" || rpt == "rptJumPotonganThn")  {
			window.open(rpt+".php" ,"pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");					
		} else {
			s = "selDateOpt.php";
			url = s + "?rpt=" + rpt;
			window.open(url ,"pop","top=100,left=100,width=500,height=300,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
		}
	}	  

	function selectPembiayaan(rpt) {
		s = "selDateOpt.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=100,width=800,height=200,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
		
	}

	function selectProject(rpt) {
		s = "selProjDate.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=100,width=800,height=200,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
		
	}

	function selectDept(rpt) {
		s = "selDeptDate.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=100,width=800,height=200,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
		
	}

	function selectBankSah(rpt) {
		s = "selBank.php";
		url = s + "?rpt=" + rpt + "&sah=1";
		window.open(url ,"pop","top=100,left=100,width=800,height=200,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
		
	}

	function selectBankTakSah(rpt) {
		s = "selBank.php";
		url = s + "?rpt=" + rpt + "&sah=0";
		window.open(url ,"pop","top=100,left=100,width=800,height=200,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
		
	}

	function selectPembiayaanAD(rpt) {
		s = "selDateOptAD.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=0,width=1200,height=200,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
		
	}  	
	
	function selectUrusniaga(rpt) {
		if (rpt == "rptD1") {
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}
		window.open(url ,"pop","top=100,left=100,width=500,height=300,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	  
	
	function selectPengurusan(rpt) {
		window.open(rpt+".php" ,"pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");					
	}	  

	function selectPenyata(rpt) {
		if (rpt == "rptG1") {
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else if (rpt == "rptG2Dept") {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		} else if (rpt=="rptPecahanPin"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else if (rpt=="rptBakiAwlAkhir"){
			url = "selYear.php?rpt="+rpt+"&id=ALL";
     	} else if (rpt=="rptSenaraiBakiAwlAkhir"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else if (rpt=="rptSenaraiUntungBulanan"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else if (rpt=="rptSenaraiBakiAkhirPem"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else if (rpt=="rptPecahanPinWajib"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		}  else if (rpt=="rptACCbank_resit"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		}  else if (rpt=="rptACCbank_baucer"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else if (rpt=="rptbank_urusniaga"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} 
		else if (rpt=="rptbank_resit"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		}
		else if (rpt=="rptB4"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		}
		else if (rpt=="rptbank_baucer"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		}
		 else {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}
		
		window.open(url ,"pop","top=100,left=100,width=750,height=300,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	  

	function selectBiaya(rpt) {

		if (rpt == "A") {
			url = "selMthYear.php?rpt=rptBiayaKeluar&id=PRBD";
		} else 	if (rpt == "B") {
			url = "selMthYear.php?rpt=rptBiayaKeluar&id=KDRN";
		} else 	if (rpt == "C") {
			url = "selMthYear.php?rpt=rptBiayaKeluar&id=BRG";
		} else	if (rpt == "F") {
			url = "selMthYear.php?rpt=rptBiayaPermohonan&id=PRBD";
		} else	if (rpt == "G") {
			url = "selMthYear.php?rpt=rptBiayaPermohonan&id=KDRN";
		} else	if (rpt == "H") {
			url = "selMthYear.php?rpt=rptBiayaPermohonan&id=BRG";
		}  else  if (rpt == "rptBakiAwlAkhir"){
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		} else  if (rpt == "rptPecahanPin"){
			url = "selYearPem.php?rpt="+rpt+"&id=ALL";
		} else	if (rpt == "rptBiayaPecahanBaki") {
			url = "selYear.php?rpt=rptBiayaPecahanBaki";
		} else{
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		}
		window.open(url ,"pop","top=100,left=100,width=650,height=300,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	
</script>';
	?>