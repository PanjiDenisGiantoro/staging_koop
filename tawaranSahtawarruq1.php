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
$sFileName		= "index.php?vw=tawaranSahtawarruq1&ID=";
$sActionFileName = "index.php?vw=tawaranSah2tawarruq&mn=3&ID=" . $ID . "";
$title     		= "Pengesahan Dokumen Tawaran Pembiayaan";
$time = time;

//$ID =  $_REQUEST['ID'];

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
$FormLabel[$a]   	= "Jumlah Pembiayaan";
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

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
$strLoan = "SELECT a.loanNo, a.loanAmt, (a.loanAmt * a.kadar_u * 0.01 * a.loanPeriod /12) as loanUntung,  a.loanAmt + (a.loanAmt * a.kadar_u * 0.01 * a.loanPeriod /12) as totalLoan, b.name, c.newIC
				FROM loans a, users b, userdetails c
				WHERE a.userID = b.userID
				AND b.userID = c.userID
				AND a.loanID =" . $ID;
$GetLoan = &$conn->Execute($strLoan);
$loanAmtTotal = number_format($GetLoan->fields(totalLoan), 2);
$noloan = $GetLoan->fields(no_sijil);
$loanAmtLo = $GetLoan->fields(totalLoan);
$loanAmt = number_format($GetLoan->fields(loanAmt), 2);
$totalshare = number_format(($loanAmtLo / 20), 2);

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($agree <> "") {
	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == "0") {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "";
		$sWhere = "loanID=" . tosql($ID, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE loans SET " .
			" isApproved=" . 1 .
			", statusT=" . 1 . ", opsyen_sah=" . tosql($opsyen_sah, "Number") .
			", approvedDate=" . tosql($updatedDate, "Text");
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);
		print '<script>
			alert ("Maklumat telah dikemaskinikan ke dalam sistem.");
			// window.close();
		//    window.opener.document.MyForm.submit();		
		window.location.href = "' . $sActionFileName . '";	
		</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
print '<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="lineBG">
<h3 class="card-title">' . strtoupper($title) . '</h3>';
//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 1) print '<div class="card-header mb-3 mt-3">i. PENGESAHAN ATAS TALIAN DOKUMEN PEMBIAYAAN DAN PERMOHONAN UNTUK MENGELUARKAN PEMBIAYAAN</div>';

	if ($cnt == 1) print '<tr valign=top>';
	print '<td class=Data align=right>' . $FormLabel[$i];
	print ':';
	print ' </td>';

	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetLoan->fields($FormElement[$i]));
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
	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td>';
	if ($cnt == 0) print '</tr>';
}
print '
<html>
<form name="MyForm" action=' . $sFileName . ' method=post>
<tr><td class=Header colspan=4><div class="card-header mt-3 mb-3">Pengesahan</div></td></tr>';
print
	'<tr>
	<td colspan=4 class=data>
<p>
1. Aku Janji (Wa&#39;d)

<br></br>Saya dengan ini merujuk kepada perkara di atas dan Tawaran Kemudahan pada tarikh yang sama dengan ini mengeluarkan AkuJanji ini. 
<br></br> Selaras dengan Tawaran Kemudahan, Saya dengan ini berakujanji untuk membeli komoditi yang dinyatakan di dalam Tawaran Kemudahan (&quot;Komoditi&quot;) daripada koperasi pada Harga Jualan yang tersebut dalam Tawaran Kemudahan (&quot;Harga Jualan&quot;) bersama-sama dengan semua kos lain yang termasuk dengannya (termasuk tetapi tidak terhad kepada kos komoditi yang dinyatakan di dalam Tawaran Kemudahan) yang kedua-duanya akan dibayar mengikut terma-terma dan syarat-syarat Tawaran Kemudahan atau apa-apa kaedah lain yang dibenarkan oleh Koperasi.
</p>
<table width="100%" border="0" cellpadding="0">
<tr><td align="left">
<input type="button" id="button1" class="btn btn-sm btn-primary" value="Ya" onclick="myFunction2()"  />
<i class="mdi mdi-check text-primary" id="img0" hidden="enabled"></i>
<input type="button" id="button2" value="Tidak" class="btn btn-sm btn-danger" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};" />

<p id="demo1"></p></td></tr></table>
	<br> 
	<p id="demo2"></p>
    <p id="demo2a"></p>
    <p id="demo2b"></p>
    <p id="demo2c"></p>
    <table width="100%" border="0" cellpadding="0">
    <tr>
    <td width="7%" align="center" valign="top"><input type="radio" hidden="enabled" name="opsyen_sah" id="check2a" value ="1" onclick="myFunction2Ra();"></td>
	<td id="demo2d" colspan="5"></td>
    </tr>
   		<tr>
        <td align="right">&nbsp;</td>
        <td id="demo2e" align="left"></td>
        <td width="18%">&nbsp;</td>
        <td width="18%">&nbsp;</td>
        <td width="18%">&nbsp;</td>
        <td width="18%">&nbsp;</td>
      	</tr>
     	 <tr>
        <td align="center" valign="top"><input type="radio" name="opsyen_sah" hidden="enabled" value ="2" id="check2b" onclick="myFunction2R();"></td>
        <td id="demo2f" colspan="5" align="left" ></td>
      	</tr>
    	</table>

    	<table width="100%" border="0" cellpadding="0">
      	<tr><td align="left">
	  	<p id="demosaham"> </p>
        <input type="button" id="button3" value="Ya" class="btn btn-sm btn-primary" hidden="enabled" onclick="myFunction3()"  />
		 <input type="button" id="button3a" value="Ya" class="btn btn-sm btn-primary" hidden="enabled" onclick="myFunction6()"  />
		<i class="mdi mdi-check text-primary" id="img1" hidden="enabled"></i>
		<i class="mdi mdi-check text-primary" id="img3a" hidden="enabled"></i>
		</td></tr></table>

		
		
	
		<p id="demo3"></p>
    	<table width="100%" border="0" cellpadding="0">
    	<tr><td align="left">
		<input type="button" id="button4" value="Ya" class="btn btn-sm btn-primary" hidden="enabled" onclick="myFunction4()"  />
		<i class="mdi mdi-check text-primary" id="img2" hidden="enabled"></i>
    	<input type="button" id="button5" value="Tidak" class="btn btn-sm btn-danger" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  />
		  
		</td></tr></table>
	
   		<p id="demo4"></p>

	<table width="100%" border="0" cellpadding="0">
      <tr><td align="left">
		  <input type="button" id="button6" value="Ya" class="btn btn-sm btn-primary" hidden="enabled" onclick="myFunction6()"  />
		  <i class="mdi mdi-check text-primary" id="img3" hidden="enabled">
          <input type="button" id="button7" value="Tidak" class="btn btn-sm btn-danger" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  />
	</td>
      </tr>
    </table>
</td></tr>';

print '<tr>
		<td class="data" align="left" colspan="4"><input type="checkbox" class="form-check-input" name="agree" id="agree" hidden ="enabled">Terma dan syarat yang dinyatakan dalam Tawaran Kemudahan, Aku Janji, Perlantikan Wakil bersama-sama dengan dokumen undang-undang yang berkaitan dengan ini mengikat diantara saya dan Koperasi</td>
		<p id="demo10"></p>
		
		</tr>';
print '<tr><td colspan=4 align=left class=Data>
			<input type="hidden" name="ID" value="' . $ID . '">
			<!--input type="button" name="kembali" class="but" value="Tutup" class="btn btn-secondary btn-sm" onClick="window.location.href=\'index.php?vw=loanApproved&mn=3\'"-->
			<input type="button" id="SubmitForm" hidden="enabled" class="btn btn-sm btn-primary" name="SubmitForm" value="Proses" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Dengan Keputusan Anda?\')) {return false} else {ITRActionButtonClickStatus(\'proses\');};">
			<input type="button" class="btn btn-sm btn-secondary" onClick="window.print()" value="Cetak"/>&nbsp;
			<br /></td></tr></table></form>
</html>';
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
	        //   window.location.href = "index.php?vw=tawaranSah2tawarruq&mn=3&ID=' . $ID . '";
	        //   window.location.href ="' . $sActionFileName . '";
			  //}
	        }
	      }
		//   window.location.href ="' . $sActionFileName . '";
	    }
function toggleTextbox(opt)
{
    if (opt == "F")
        document.getElementByID("txtText").disabled = false;
    else
        document.getElementByID("txtText").disabled = true;
}

function myFunction() {
   // document.getElementById("demo").innerHTML = "blah blah";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = false;
}
function myFunction1() {
    document.getElementById("demoTidak").innerHTML = "blah";
	document.getElementById("button2").disabled = true;
	document.getElementById("button1").disabled = false;
}

function myFunction2() {

	document.getElementById("demo2a").innerHTML = "2. Pemilihan Pembelian (Opsyen)";
	document.getElementById("demo2b").innerHTML = "Saya dengan ini memilih untuk :";
	document.getElementById("demo2c").innerHTML = "(sila tick yang mana berkenaan)";
	document.getElementById("demo2d").innerHTML = "<b><u>(PENGAMBILAN TUNAI)</u></b> </br> Meninggalkan komoditi itu kepada koperasi dan menyerahkan kuasa kepada Koperasi sebagai ejen saya untuk menjual komoditi itu kepada pihak pembeli ketiga pada harga kos dan dengan itu mengkreditkan hasil jualan komoditi (jumlah pembiayaan) ke dalam akaun saya. ";
	document.getElementById("demo2e").innerHTML = "<strong>ATAU</strong>";
	document.getElementById("demo2f").innerHTML = "<b><u>(PENGAMBILAN KOMODITI)</u></b> </br>Menerima penghantaran komoditi, atau menjual komoditi kepada mana-mana pihak ketiga atas kehendak saya, dengan segala kos berkaitan ditanggung oleh saya sendiri, Saya dengan ini melepaskan Koperasi daripada segala tanggungan dan berakujanji untuk menanggung rugi Koperasi terhadap apa-apa tindakan undang-undang termasuk menanggungrugi kesemua kos dan perbelanjaan atau kerosakan yang Koperasi mungkin alami atau tanggung berikutan dengan pilihan ini.";
	    //document.getElementById("demo1").innerHTML = "Baca Dulu.";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
	
	document.getElementById("check2a").hidden = false;
	document.getElementById("check2b").hidden = false;
	document.getElementById("img0").hidden = false;

}

function myFunction2Ra() {

    document.getElementById("button3").hidden = false;
	document.getElementById("demosaham").innerHTML = "";
	document.getElementById("demosaham").hidden = false;
	document.getElementById("img3a").style.display = "none";
	document.getElementById("check2b").hidden = true;

}
function myFunction2R() {
	document.getElementById("button3a").hidden = false;
	document.getElementById("demosaham").hidden = true;
	
	
	document.getElementById("demo3").hidden = true;
	document.getElementById("demo4").hidden = true;
	
	document.getElementById("button4").style.display = "none";
	document.getElementById("button5").style.display = "none";
	document.getElementById("img2").style.display = "none";
	
	document.getElementById("button6").style.display = "none";
	document.getElementById("button7").style.display = "none";
	document.getElementById("img3").style.display = "none";
	document.getElementById("check2a").hidden = true;
	
	
}
function myFunction3() {
	
    document.getElementById("demo3").innerHTML = "3. Pelantikan Wakil. </br></br> Saya dengan ini secara muktamadnya melantik Koperasi untuk menjadi wakil saya untuk menjual komoditi yang dinyatakan di dalam Surat Tawaran (&#34;Komoditi&#34;) kepada mana-mana pembeli pihak ketiga yang difikirkan sesuai oleh Koperasi pada harga yang dinyatakan di dalam Surat Tawaran (&#34;Jumlah Pembiayaan&#34;) sahaja. Sila serahkan hasil daripada penjualan itu ke dalam Akaun Bank saya yang telah dinyatakan di dalam AkuJanji yang dikeluarkan oleh saya kepada Koperasi, yang telah ditetapkan bagi tujuan Kemudahan Pembiayaan Peribadi-i. </br></br> Saya akan terikat dengan mana-mana kontrak atau perjanjian yang melibatkan pembeli pihak ketiga untuk tujuan penjualan komoditi bagi pihak saya. </br></br> Saya dengan ini mengaku janji untuk membayar ganti rugi kepada Koperasi atas segala kerugian, kos, perbelanjaan atau kerosakan yang mungkin dialami atau ditanggung oleh Koperasi akibat memenuhi keperluan sebagai wakil saya.";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
	document.getElementById("button3").disabled = true;
	document.getElementById("button4").hidden = false;
	document.getElementById("button5").hidden = false;
	document.getElementById("img1").hidden = false;
	
}

function myFunction4() {
	
document.getElementById("demo4").innerHTML = "4. Kontrak Jualan Murabahah. </br></br> Berdasarkan kepada Surat Tawaran dan AkuJanji yang dikeluarkan oleh saya kepada Koperasi, Koperasi dengan ini menjual dan saya dengan ini membeli komoditi (butiran yang mana adalah seperti yang dinyatakan di dalam Sijil Komoditi) (&#34;Komoditi&#34;) pada harga jualan yang dinyatakan pada Surat Tawaran (&#34;Harga Jualan&#34;) yang merangkumi Harga Kos Koperasi dan margin keuntungan dengan pembayaran secara tangguh tertakluk kepada terma dan syarat di dalam Surat Tawaran.";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
	document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").hidden = false;
	document.getElementById("button7").hidden = false;
	document.getElementById("img2").hidden = false;
	
}

function myFunction6() {
	document.getElementById("demo10").innerHTML = "";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
	document.getElementById("button3").disabled = true;
	document.getElementById("button3a").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").disabled = true;
	document.getElementById("button7").disabled = true;
	document.getElementById("agree").hidden = false;
	document.getElementById("SubmitForm").hidden = false;
	document.getElementById("img3").hidden = false;
	document.getElementById("img3a").hidden = false;
	
}</script>';
