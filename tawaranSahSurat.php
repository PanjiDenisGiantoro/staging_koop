<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *          Date 		: 	
 *********************************************************************************/
session_start();
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$sFileName		= "tawaranSahSurat.php";
$sActionFileName = "index.php?vw=tawaranSahtawarruq1&mn=3&ID=" . $ID . '';
$title     		= "Pengesahan Dokumen Tawaran Pembiayaan";




//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
$strErrMsg = array();

$a = 1;
$FormLabel[$a]   	= "Nama Penuh";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "No KTP Baru";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "Jumlah Diluluskan";
$FormElement[$a] 	= "loanAmt";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Nomor Rujukan";
$FormElement[$a] 	= "loanNo";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$komo = "SELECT * from komoditi where loanID=" . $ID;
$Getkomo = &$conn->Execute($komo);

$loandocs = "SELECT * FROM loandocs WHERE loanID=" . $ID;
$getloandocs = &$conn->Execute($loandocs);


//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
$strLoan = "SELECT a.loanNo, a.loanAmt, (a.loanAmt * a.kadar_u * 0.01 * a.loanPeriod /12) as loanUntung,  a.loanAmt + (a.loanAmt * a.kadar_u * 0.01 * a.loanPeriod /12) as totalLoan, b.name, c.newIC,a.approvedDate,a.userID,a.loanType,a.kadar_u,a.loanPeriod,a.monthlyPymt,a.pokokAkhir,a.untungAkhir,
a.penjaminID1,a.penjaminID2,a.penjaminID3
				FROM loans a, users b, userdetails c
				WHERE a.userID = b.userID
				AND b.userID = c.userID
				AND a.loanID =" . $ID;
$GetLoan = &$conn->Execute($strLoan);
$userID = $GetLoan->fields(userID);

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "") {
	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == "0") {
		//$updatedBy 	= get_session("Cookie_userName");
		//$updatedDate = date("Y-m-d H:i:s");               
		$sSQL = "";
		$sWhere = "";
		$sWhere = "loanID=" . tosql($ID, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE loans SET stat_agree='2'";
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);
		//$activity = "Mengemaskini maklumat pembiayaan anggota";
		//if($rs) activityLog($sSQL, $activity, get_session('Cookie_userID'), get_session("Cookie_userName"));		
		print '<script>
					alert ("Maklumat telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
					//window.close();
				   //window.opener.document.MyForm.submit();

				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<h3 class="card-title">' . strtoupper($title) . '</h3>';
//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 1) print '<div class="card-header mb-3 mt-3">i. PENGESAHAN ATAS TALIAN DOKUMEN PEMBIAYAAN TELAH LENGKAP DAN SEMPURNA SERTA PERMOHONAN UNTUK MENGELUARKAN PEMBIAYAAN</div>';

	if ($cnt == 1) print '<tr>';
	print '<td class=Data>' . $FormLabel[$i];
	print ':';
	print ' </td>';

	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetLoan->fields($FormElement[$i]));
	//if ($strFormValue == '') $strFormValue = $$FormElement[$i];	

	if ($FormType[$i] == 'textarea') {
		$strFormValue = str_replace("<pre>", "", $GetLoan->fields($FormElement[$i]));
		$strFormValue = str_replace("</pre>", "", $strFormValue);
	}

	if ($i == 3) {
		$strFormValue = number_format($GetLoan->fields($FormElement[$i]), 2);
	}
	FormEntry(
		$FormLabel[$i],
		$FormElement[$i],
		$FormType[$i],
		$strFormValue,
		$FormData[$i],
		$FormDataValue[$i],
		$FormSize[$i],
		$FormLength[$i]
	);

	//if($i == 3){
	//$dept = dlookup("loans", "loanAmt", "loanID=" . $GetLoan->fields(departmentID));
	//print $dept;
	//}

	$loanAmt = number_format($GetLoan->fields(loanAmt), 2);
	$loanAmt2 = $GetLoan->fields(loanAmt);
	$namaloan = dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
	$kadar_u = $GetLoan->fields(kadar_u);
	$loanperiod = ($GetLoan->fields(loanPeriod) / 12);
	$loanperiod2ndlast = ($GetLoan->fields(loanPeriod) - 1);
	$loanperiodlast = $GetLoan->fields(loanPeriod);
	$jum_biayauntung = dlookup("loandocs", "lpotBiayaN", "loanID=" . tosql($getloandocs->fields(loanID), "Number"));
	$itemType = dlookup("general", "name", "ID=" . tosql($Getkomo->fields(itemType), "Number"));
	$monthlyPymt = $GetLoan->fields(monthlyPymt);


	$pokokAkhir = $GetLoan->fields(pokokAkhir);
	$untungAkhir = $GetLoan->fields(untungAkhir);
	$jumlah_bayarbln_akhir = ($pokokAkhir + $untungAkhir);

	$btindihCaj = dlookup("loandocs", "btindihCaj", "loanID=" . tosql($getloandocs->fields(loanID), "Number"));
	$jum_biayauntungq = ($loanAmt2 * (0.1 * $loanperiod));
	$jum_biayauntungtotal =  $jum_biayauntungq + $loanAmt2;



	$penjamin1 = dlookup("users", "name", "userID=" . tosql($GetLoan->fields(penjaminID1), "Number"));
	$penjamin2 = dlookup("users", "name", "userID=" . tosql($GetLoan->fields(penjaminID2), "Number"));
	$penjamin3 = dlookup("users", "name", "userID=" . tosql($GetLoan->fields(penjaminID3), "Number"));

	$kppenjamin1 = dlookup("userdetails", "newIC", "userID=" . tosql($GetLoan->fields(penjaminID1), "Number"));
	$kppenjamin2 = dlookup("userdetails", "newIC", "userID=" . tosql($GetLoan->fields(penjaminID2), "Number"));
	$kppenjamin3 = dlookup("userdetails", "newIC", "userID=" . tosql($GetLoan->fields(penjaminID3), "Number"));

	$userpenjamin1 = dlookup("users", "userID", "userID=" . tosql($GetLoan->fields(penjaminID1), "Number"));
	$userpenjamin2 = dlookup("users", "userID", "userID=" . tosql($GetLoan->fields(penjaminID2), "Number"));
	$userpenjamin3 = dlookup("users", "userID", "userID=" . tosql($GetLoan->fields(penjaminID3), "Number"));

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	//<input type="text" name="sellUserName" class="Data" value="'.$sellUserName.'" onfocus="this.blur()" size="50">   
	print '&nbsp;</td><br/>';
	if ($cnt == 0) print '</tr>';
}

print '
<html>
<form name="MyForm" action=' . $sFileName . ' method=post>
<tr><td class=Header colspan=4><div class="card-header mt-3 mb-3">Pengesahan</div></td></tr>';

///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

print '<center><tr>
<td colspan=4 class=data align=center>
<b><u>PERMOHONAN KEMUDAHAN PEMBIAYAAN PERIBADI-i SEBANYAK RM ' . $loanAmt . '</u></b>

<br>Sukacita dimaklumkan bahawa [NAMA KOPERASI] (selepas ini disebut sebagai "Koperasi") akan menawarkan kemudahan ' . $namaloan . ' tertakluk kepada syarat seperti berikut:-</br></center>
<br>
<tr>
<td class="padding1" valign="top"><b>1.0</b></td><td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>KONSEP SYARIAH</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top"><b>Tawarruq</b></td>
</tr>
<br/><br/>
<tr>
  <td class="padding1" valign="top"><b>2.0</b></td>
  <td>&nbsp;</td>
  <td class="padding1" valign="top" colspan="2"><b><u>JUMLAH PEMBIAYAAN</u></b></td>
  <td class="padding1" valign="top">&nbsp;:&nbsp;</td>
  <td class="padding1" valign="top" ><b>RM  ' . $loanAmt . '</b></td>
</tr>
<br/><br/>
<tr>
  <td class="padding1" valign="top"><b>3.0</b></td>
  <td>&nbsp;</td>
  <td class="padding1" valign="top" colspan="2"><b><u>KADAR KEUNTUNGAN (SILING)</u></b></td>
  <td class="padding1" valign="top">&nbsp;:&nbsp;</td>
  <td class="padding1" valign="top" ><b>10%</b></td>
</tr>
<br/><br/>
<tr>
<td class="padding1" valign="top"><b>4.0</b></td><td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>HARGA JUALAN</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top"><b>RM ' . number_format($jum_biayauntungtotal, 2) . '</b></td>
</tr>
<br/><br/>
<tr>
<td class="padding1" valign="top"><b>5.0</b></td><td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>MAKLUMAT ASET / KOMODITI</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top"><b>' . $itemType . '</b></td>
</tr>
<br/><br/>
<tr>
<td class="padding1" valign="top"><b>6.0</b></td>
<td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>TEMPOH BAYARAN ANSURAN</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top"><b>' . $loanperiod . ' TAHUN</b></td>
</tr>
<br/><br/>
<tr>
<td class="padding1" valign="top"><b>7.0</b></td>
<td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>BAYARAN</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top">Tuan/Puan dikehendaki membayar ansuran bulanan sebanyak RM ' . $monthlyPymt . ' sebulan dari bulan <b><u>1</u></b> ke bulan <b><u>' . $loanperiod2ndlast . '</u></b> dan RM ' . $jumlah_bayarbln_akhir . ' untuk bulan ke <b><u>' . $loanperiodlast . '</u></b>.<br /> 
Ansuran pertama bermula pada bulan berikutnya jika pembiayaan dikeluarkan selepas/pada 16 haribulan ansuran seterusnya hendaklah dibayar pada bulan berikutnya sehingga kesemua harga jualan dijelaskan sepenuhnya.</td>
</tr>
<br/><br/>
<tr>
<td class="padding1" valign="top"><b>8.0</b></td>
<td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>CARA BAYARAN</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top">Ansuran bulanan hendaklah dibayar melalui <b><u>potongan gaji bulanan</u></b>. Sekiranya potongan gaji bulanan tidak diperolehi, angggota membenarkan [NAMA KOPERASI] untuk memotong melalui <b><u>Potongan Akaun Tabungan</u></b>.</td>
</tr>
<br/><br/>
<tr>
<td class="padding1" valign="top"><b>9.0</b></td>
<td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>BAYARAN PERKHIDMATAN</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top">Tuan/Puan dikehendaki menjelaskan bayaran perkhidmatan sebanyak RM ' . $btindihCaj . ' pada masa menerima tawaran ini.</td>
</tr>
<br/><br/>
<tr>
<td class="padding1" valign="top"><b>10.0</b></td>
<td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>JAMINAN/CAGARAN</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top" >Jaminan individu oleh :<br/>
<center><tr>
<td class="padding1" valign="top" width="30">i)</td>
<td class="padding1" valign="top">Nama Penjamin</td>
<td class="padding1" valign="top">&nbsp;:&nbsp;' . $penjamin1 . '</td>
</tr>
<br/>
<tr>
<td valign="top">&nbsp;</td>
<td class="padding1" valign="top">Nombor Kartu Identitas</td>
<td class="padding1" valign="top">&nbsp;:&nbsp;' . $kppenjamin1 . '</td>
</tr>
<br/>
<tr>
<td valign="top">&nbsp;</td>
<td class="padding1" valign="top">Nomor Anggota</td>
<td class="padding1" valign="top">&nbsp;:&nbsp;' . $userpenjamin1 . '</td>
</tr>
<br/><br/>
<tr>
<td class="padding1" valign="top" width="30">ii)</td>
<td class="padding1" valign="top">Nama Penjamin</td>
<td class="padding1" valign="top">&nbsp;:&nbsp;' . $penjamin2 . '</td>
</tr>
<br/>
<tr>
<td valign="top">&nbsp;</td>
<td class="padding1" valign="top">Nombor Kartu Identitas</td>
<td class="padding1" valign="top">&nbsp;:&nbsp;' . $kppenjamin2 . '</td>
</tr>
<br/>
<tr>
<td valign="top">&nbsp;</td>
<td class="padding1" valign="top">Nomor Anggota</td>
<td class="padding1" valign="top">&nbsp;:&nbsp;' . $userpenjamin2 . '</td>
</tr>
<br/><br/>
<tr>
<td class="padding1" valign="top" width="30">iii)</td>
<td class="padding1" valign="top">Nama Penjamin</td>
<td class="padding1" valign="top">&nbsp;:&nbsp;' . $penjamin3 . '</td>
</tr>
<br/>
<tr>
<td valign="top">&nbsp;</td>
<td class="padding1" valign="top">Nombor Kartu Identitas</td>
<td class="padding1" valign="top">&nbsp;:&nbsp;' . $kppenjamin3 . '</td>
</tr>
<br/>
<tr>
<td valign="top">&nbsp;</td>
<td class="padding1" valign="top">Nomor Anggota</td>
<td class="padding1" valign="top">&nbsp;:&nbsp;' . $userpenjamin3 . '</td>
</tr>
</td>
</tr>
</center>
<br/>
<tr>
<td class="padding1" valign="top"><b>11.0</b></td><td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>PENJELASAN AWAL & IBRA&#39;</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top">Tuan/Puan boleh menjelaskan Harga Jualan dan lain-lain kos sebenar (sekiranya ada) sepenuhnya pada bila bila masa dan Pihak Koperasi berhak atas budibicara untuk memberikan Ibra&#39; (rebat) kepada tuan/puan di atas penjelasan awal tersebut. <br />Berikut adalah formula penyelesaian awal: <br></br>
Jumlah Penyelesaian Awal = 
Harga Jualan - Ansuran yang dibayar - Rebat (keuntungan belum terakru) + keuntungan sebulan + caj perkhidmatan (SEKIRANYA ADA)</td><br/><br/>
</tr>
<tr>
<td class="padding1" valign="top"><b>12.0</b></td><td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>PENGHANTARAN NOTIS</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top">Sebarang pertukaran alamat hendaklah segera dimaklumkan kepada [NAMA KOPERASI] secara bertulis. Sebarang surat menyurat, notis atau tuntutan yang dihantar secara pos biasa atau berdaftar menggunakan alamat tersebut di atas atau alamat terakhir yang diketahui oleh [NAMA KOPERASI] hendaklah dianggap telah disampaikan dan diterima pada masa dan dengan cara pada kebiasaannya surat tersebut diserahkan.</td>
</tr>
<br/><br/>
<tr>
<td class="padding1" valign="top"><b>13.0</b></td><td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>PENYAMPAIAN PROSES PERUNDANGAN</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top">
Penyampaian semua proses undang-undang, adalah dianggap telah disampaikan dan diterima sekiranya telah diposkan melalui pos berdaftar yang dialamat kepada tuan/puan di alamat yang dinyatakan di dalam surat tawaran ini atau kepada alamat terakhir yang diberikan olah tuan/puan secara bertulis kepada kami dan proses tersebut sama ada saman atau sebaliknya adalah dianggap telah disampaikan dan diterima selepas tiga (3) hari ia diposkan.
</td>
<br/><br/>
<tr>
<td class="padding1" valign="top"><b>14.0</b></td><td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>PAMPASAN (TA&#39;WIDH)</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td class="padding1" valign="top">
Sekiranya Pelanggan lewat membayar mana-mana ansuran sebagaimana yang ditetapkan di dalam Surat Tawaran ini, Pelanggan adalah bertanggungjawab untuk membayar pampasan kepada [NAMA KOPERASI] dikira pada kadar satu peratus (1%) setahun atas amaun tertunggak sepertimana ketetapan Bank Negara Malaysia. Amaun pampasan terhadap kelewatan membayar adalah dikira dari tarikh luput bayaran ansuran sehingga bayaran diterima oleh [NAMA KOPERASI].
</td>
</tr>
<br/><br/>
<tr>
<td class="padding1" valign="top"><b>15.0</b></td><td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top" width="120" colspan="2"><b><u>SYARAT-SYARAT LAIN</u></b></td>
<td class="padding1" valign="top">&nbsp;:&nbsp;</td>
<td valign="top">
<tr>
<br/>
<td class="padding1" valign="top" width="30">15.1</td><td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top">
[NAMA KOPERASI] berhak memaklumkan status kedudukan pembiayaan ini kepada penjamin-penjamin pada bila-bila masa diperlukan sama ada bertulis atau lisan.(Sekiranya berkenaan)
</td>
</tr>
<br/>
<tr>
<td class="padding1" valign="top" width="30">15.2</td><td>&nbsp;&nbsp;</td>
<td class="padding1" valign="top">
Adalah menjadi satu syarat pembiayaan ini bahawa tuan/puan memberikan persetujuan kepada pihak [NAMA KOPERASI] untuk memasukkan segala maklumat berkenaan pembiayaan tuan/puan kedalam sistem daftar <i>Financial Information Services Sdn Bhd (FIS)</i> dan/atau mana-mana syarikat lain atau agensi yang seumpama dengannya untuk kegunaan semua ahli atau penyumbang kepada syarikat/agensi tersebut.
Walaubagaimanapun, maklumat tersebut adalah disimpan secara sulit oleh pihak syarikat/agensi dan semua ahli/penyumbang.
</td>
</tr>
</td>
</tr>


<center>
<br />
<p>Sekian, terima kasih.</p>

<p>Yang Benar,<br /></p>
Setiausaha<br /><br /><i>Surat ini dicetak oleh komputer dan tidak memerlukan tandatangan</i></p>
<p><u><b>PENERIMAAN</u></b></p>
<p>Saya bersetuju menerima tawaran ini dengan syarat-syarat seperti di atas. Saya juga mengaku bahawa saya tidak diisytiharkan muflis semasa tawaran ini.</p>
<br /><br /><br /><br /><br />

			</td>
		</tr>';

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


print '<tr>
		</tr>';
print '<tr><td colspan=4 align=center class=Data>
			<input type="hidden" name="ID" value="' . $ID . '">
</td></tr>';

print ' 	<tr><td colspan="4" class="Data"><br></br></td></tr>
		<tr align="center"><td colspan="4" class="Data">
		

<input type="Submit" name="SubmitForm" value="Setuju" class="btn btn-primary">

<input type="button" value="Tidak Setuju" class="btn btn-danger" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  />
</center>
</td></tr>
</form>';

include("footer.php");
print '
<script language="JavaScript">

	function ITRActionButtonClickStatus(v) {
	      var strStatus="";
		  e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        j=0;
			for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="agree" && e.elements[c].checked) {
				pk = e.elements[c].value;
				//strStatus = strStatus + ":" + pk;
				count++;
	          }
	        }
	        
	        if(count==0) {
	          //alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          //if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          e.submit();
	          //window.location.href ="memberApply.php?pk=" + strStatus;
	          //window.location.href ="' . $sActionFileName . '";
			  //}
	        }
	      }
	    }

</script>';
