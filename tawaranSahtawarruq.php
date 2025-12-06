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
$sFileName		= "tawaranSahtawarruq.php";
$sActionFileName = "loanView.php";
$title     		= "Pengesahan Dokumen Tawaran Pembiayaan";
$time = time;
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
$strLoan = "SELECT a.loanNo, a.loanAmt, (a.loanAmt * a.kadar_u * 0.01 * a.loanPeriod /12) as loanUntung,  a.loanAmt + (a.loanAmt * a.kadar_u * 0.01 * a.loanPeriod /12) as totalLoan, b.name, c.newIC,d.no_sijil
				FROM `loans` a, users b, userdetails c,komoditi d
				WHERE a.userID = b.userID
				AND b.userID = c.userID
				AND a.loanID = d.loanID
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
			window.close();
		   window.opener.document.MyForm.submit();
		</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
print '<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="lineBG">
<tr><td colspan="4" class="Data"><b class="maroonText">' . strtoupper($title) . '</b></td></tr>';
//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 1) print '<tr><td class=Header colspan=4>i. PENGESAHAN ON-LINE DOKUMEN PEMBIAYAAN DAN PERPOHONAN UNTUK MENGELUARKAN PEMBIAYAAN</td></tr>';

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
<tr><td class=Header colspan=4>Pengesahan</td></tr>';
print '<tr>
  <td colspan=4 class=data>
    <p>1. Saya dengan ini merujuk kepada perkara di atas dan Tawaran Kemudahan pada tarikh yang sama dengan ini mengeluarkan AkuJanji ini.</p>
    <table width="100%" border="0" cellpadding="0">
    <tr><td align="left">
	<input type="button" id="button1" value="YA" onclick="myFunction2()"  />
	<img src="images/sym-tick-red-bkrm-01.gif" id="img0" width="20" height="20" hidden="enabled">
    <input type="button" id="button2" value="TIDAK" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};" />
		
	<a id="buttontawaran" href="letter.php?group=7&code=125&id=' . $ID . '&type=surat&head=1","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no;menubar=no" hidden="enabled" target="_blank">(Tawaran Kemudahan) --</a>
		
	<a id="buttonjanji" href="letter.php?group=7&code=127&id=' . $ID . '&type=surat&head=1","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no;menubar=no" hidden="enabled" target="_blank">(Aku Janji Wa\'d) <b>(SILA BACA SURAT TERLEBIH DAHULU)</b></a>
				
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
        <td align="center" valign="top"><input type="radio" name="opsyen_sah" hidden="enabled" value ="2" id="check2b" onclick="myFunction2R();" ></td>
        <td id="demo2f" colspan="5" align="left"></td>
      </tr>
    </table>

    	<table width="100%" border="0" cellpadding="0">
      	<tr><td align="left">
	  	<p id="demosaham"> </p>
        <input type="button" id="button3" value="YA" hidden="enabled" onclick="myFunction3()"  />
		<img src="images/sym-tick-red-bkrm-01.gif" id="img1" width="20" height="20" hidden="enabled">
</td></tr></table>
	
	<p id="demo3"></p>
    <table width="100%" border="0" cellpadding="0">
    <tr><td align="left">
	<input type="button" id="button4" value="YA" hidden="enabled" onclick="myFunction4()"  />
	<img src="images/sym-tick-red-bkrm-01.gif" id="img2" width="20" height="20" hidden="enabled">
    <input type="button" id="button5" value="TIDAK" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  />
		  
</td></tr></table>
	
   	<p id="demo4"></p>
	<p id="demo4b"></p>
			
    <table width="100%" border="0" cellpadding="0">
    <tr><td align="left">
	<input type="button" id="button6" value="YA" hidden="enabled" onclick="myFunction4a()"  />
	<img src="images/sym-tick-red-bkrm-01.gif" id="img3" width="20" height="20" hidden="enabled">
    <input type="button" id="button7" value="TIDAK" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  /></td>
      </tr>
	  <tr><td></td></tr>
	  <tr><td></td></tr>
	  <tr>
	  <td>
		  <a id="buttonjualan" href="letter.php?group=7&code=99&id=' . $ID . '&type=surat&head=1","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no;menubar=no" hidden="enabled" target="_blank">(Surat Perjanjian Jualan) --</a>
		  
		  		<a id="buttonkomoditi" href="letter.php?group=7&code=124&id=' . $ID . '&type=surat&head=1","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no;menubar=no" hidden="enabled" target="_blank">(Sijil Komoditi) --</a>
				  
				  		  <a id="buttonjadual" href="letter.php?group=7&code=126&id=' . $ID . '&type=surat&head=1","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no;menubar=no" hidden="enabled" target="_blank">(Jadual)</a>
						  
		<p id="demo4c"></p> </p>
		 
	    </td>
      </tr>
    </table>

	<table width="100%" border="0" cellpadding="0">
      <tr><td align="left">
	  <p id="demo4a"></p>
		  <input type="button" id="button4a" value="Klik Disini." hidden="enabled" onclick="myFunction5()"/>
		  <img src="images/sym-tick-red-bkrm-01.gif" id="img11" width="20" height="20" hidden="enabled">
		</td>
      </tr>
    </table>
	
	<p id="demo5"></p>
	<table width="100%" border="0" cellpadding="0">
      <tr><td align="left">
		  <input type="button" id="button8" value="YA" hidden="enabled" onclick="myFunction6()"  />
		   <img src="images/sym-tick-red-bkrm-01.gif" id="img4" width="20" height="20" hidden="enabled">
          <input type="button" id="button9" value="TIDAK" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  />
	</td>
      </tr>
    </table>
	
		<table width="100%" border="0" cellpadding="0">
      <tr><td align="left">
		  <input type="button" id="button22" value="YA" hidden="enabled" onclick="myFunction9()"  />
		   <img src="images/sym-tick-red-bkrm-01.gif" id="img9" width="20" height="20" hidden="enabled">
          <input type="button" id="button23" value="TIDAK" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  />
	</td>
      </tr>
    </table>



	<table width="100%" border="0" cellpadding="0">
      <tr><td align="left">
	  <p id="demo6"></p>
	 <a id="buttonagensi" href="letter.php?group=7&code=119&id=' . $ID . '&type=surat&head=1","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no;menubar=no" hidden="enabled" target="_blank">Surat Perlantikan Wakalah (Agensi) <b>(SILA BACA SURAT TERLEBIH DAHULU)</b></a>
		  
	<p id="demo6a"></p>
		 <input type="button" id="button6a" value="Klik Disini." hidden="enabled" onclick="myFunction7()"/>
		 <img src="images/sym-tick-red-bkrm-01.gif" id="img12" width="20" height="20" hidden="enabled">
		  <input type="button" id="button6b" value="Pengesahan Komoditi" hidden="enabled" onclick="myFunction9()"/>
		  
		  
		
		 
		</td>
      </tr>
    </table>

		
		
		
		<p id="demo7"></p>
		<p id="demo7a"></p>
		<p id="demo7b"></p>
    <table width="100%" border="0" cellpadding="0">
      <tr><td align="left">
		  <input type="button" id="button10" value="YA" hidden="enabled" onclick="myFunction9()"  />
		   <img src="images/sym-tick-red-bkrm-01.gif" id="img5" width="20" height="20" hidden="enabled">
          <input type="button" id="button11" value="TIDAK" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  /></td>
</tr></table>
	
<p id="demo9"></p>
   <table width="100%" border="0" cellpadding="0">
     <tr><td align="left">
	  <input type="button" id="button12" value="YA" hidden="enabled" onclick="myFunction10()"  />
	   <img src="images/sym-tick-red-bkrm-01.gif" id="img6" width="20" height="20" hidden="enabled">
       <input type="button" id="button13" value="TIDAK" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"/></td></tr></table></td></tr>';

print '<tr>
		<td class="data" align="left" colspan="4"><input type="checkbox" class="form-check-input" name="agree" id="agree" hidden ="enabled">Terma dan syarat yang dinyatakan dalam Tawaran Kemudahan, Aku Janji, Perlantikan Wakil bersama-sama dengan dokumen undang-undang yang berkaitan dengan ini mengikat diantara saya dan Koperasi</td>
		<p id="demo10"></p>
		
		</tr>';
print '<tr><td colspan=4 align=left class=Data>
			<input type="hidden" name="ID" value="' . $ID . '">
			<input type=button name=kembali class="but" value=Tutup onClick="window.close();">
			<input type="button" id="SubmitForm" hidden="enabled" class="but" name="SubmitForm" value="Proses" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Dengan Keputusan Anda?\')) {return false} else {ITRActionButtonClickStatus(\'proses\');};">
			<input type="button" onClick="window.print()" value="Print"/>&nbsp;
			<br /></td></tr></table></form>
</html>';
//"ITRActionButtonClickStatus(\'proses\');"
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

	document.getElementById("demo2a").innerHTML = "2. Pemilihan Pembelian";
	document.getElementById("demo2b").innerHTML = "Saya dengan ini memilih untuk :";
	document.getElementById("demo2c").innerHTML = "(sila tick yang mana berkenaan)";
	document.getElementById("demo2d").innerHTML = "Meninggalkan komoditi itu kepada koperasi dan menyerahkan kuasa kepada Koperasi sebagai ejen saya untuk menjual komoditi itu kepada pihak pembeli ketiga pada harga kos dan dengan itu mengkreditkan hasil jualan komoditi (jumlah pembiayaan) ke dalam akaun saya. <b><u>(PENGAMBILAN TUNAI)</u></b>";
	document.getElementById("demo2e").innerHTML = "<strong>ATAU</strong>";
	document.getElementById("demo2f").innerHTML = "Menerima penghantaran komoditi, atau menjual komoditi kepada mana-mana pihak ketiga atas kehendak saya, dengan segala kos berkaitan ditanggung oleh saya sendiri, Saya dengan ini melepaskan Koperasi daripada segala tanggungan dan berakujanji untuk menanggung rugi Koperasi terhadap apa-apa tindakan undang-undang termasuk menanggungrugi kesemua kos dan perbelanjaan atau kerosakan yang Koperasi mungkin alami atau tanggung berikutan dengan pilihan ini. <b><u>(PENGAMBILAN BARANG KOMODITI)</u></b>";
	    //document.getElementById("demo1").innerHTML = "Baca Dulu.";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
	document.getElementById("buttontawaran").hidden = false;
	document.getElementById("buttonjanji").hidden = false;
	document.getElementById("check2a").hidden = false;
	document.getElementById("check2b").hidden = false;
	document.getElementById("img0").hidden = false;

}

function myFunction2Ra() {

    document.getElementById("button3").hidden = false;
	document.getElementById("demosaham").innerHTML = "";
	document.getElementById("demosaham").hidden = false;
	
	document.getElementById("demo6").hidden = true;
	document.getElementById("demo6a").hidden = true;
	document.getElementById("demo7").hidden = true;
	document.getElementById("demo7a").hidden = true;
	document.getElementById("demo7b").hidden = true;
	document.getElementById("buttonagensi").style.display = "none";
	document.getElementById("button6a").style.display = "none";
	document.getElementById("img5").style.display = "none"; 
	document.getElementById("button10").style.display = "none";
	document.getElementById("button11").style.display = "none";
	 
	document.getElementById("button8").style.display = "none";
	document.getElementById("button9").style.display = "none";

}
function myFunction2R() {
	document.getElementById("button3").hidden = false;
	document.getElementById("demosaham").hidden = true;
	document.getElementById("button6b").style.display = "none";
	
	document.getElementById("button22").style.display = "none";
	document.getElementById("button23").style.display = "none";
	document.getElementById("img9").style.display = "none";
}
function myFunction3() {
	
    document.getElementById("demo3").innerHTML = "3. Selaras dengan Tawaran Kemudahan, Saya dengan ini berakujanji untuk membeli komoditi yang dinyatakan di dalam Tawaran Kemudahan (&quot;Komoditi&quot;) daripada koperasi pada Harga Jualan yang tersebut dalam Tawaran Kemudahan (&quot;Harga Jualan&quot;) bersama-sama dengan semua kos lain yang termasuk dengannya (termasuk tetapi tidak terhad kepada kos komoditi yang dinyatakan di dalam Tawaran Kemudahan) yang kedua-duanya akan dibayar mengikut terma-terma dan syarat-syarat Tawaran Kemudahan atau apa-apa kaedah lain yang dibenarkan oleh Koperasi.";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
	document.getElementById("button3").disabled = true;
	document.getElementById("button4").hidden = false;
	document.getElementById("button5").hidden = false;
	
	document.getElementById("buttontawaran").hidden = false;
	document.getElementById("buttonjanji").hidden = false;
	document.getElementById("img1").hidden = false;
}



function myFunction4() {
	
   document.getElementById("demo4").innerHTML = "4. Jualan Komoditi.";
	document.getElementById("demo4b").innerHTML = "(a) Berdasarkan kepada Tawaran Kemudahan serta Aku Janji yang diterima, Koperasi dengan ini menjual pada harga jualan yang dinyatakan di dalam Perjanjian Jualan yang merangkumi Harga Belian Koperasi di dalam Perjanjian ini dan margin keuntungan dengan pembayaran secara tangguh tertakluk kepada terma dan syarat di dalam Tawaran Kemudahan.";
	
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
	document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").hidden = false;
	document.getElementById("button7").hidden = false;	
	document.getElementById("img2").hidden = false;
}


function myFunction4a() {
	
 	document.getElementById("demo4a").innerHTML = "Saya dengan ini telah membaca Surat Perjanjian Jualan, Sijil Komoditi, dan Jadual serta bersetuju di atas pembelian di dalam surat tersebut." ;
	document.getElementById("demo4c").innerHTML = "<b>SILA BACA SURAT TERLEBIH DAHULU</b>";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
    document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").disabled = true;
	document.getElementById("button7").disabled = true;
	document.getElementById("button4a").hidden = false;
	document.getElementById("buttonjualan").hidden = false;
	document.getElementById("buttonkomoditi").hidden = false;
	document.getElementById("buttonjadual").hidden = false;
	document.getElementById("img3").hidden = false;
}

function myFunction5() {
	
 	document.getElementById("demo5").innerHTML = "(b) Berdasarkan kepada Tawaran Kemudahan dan Aku Janji yang dikeluarkan, saya dengan ini membeli komoditi pada harga jualan yang dinyatakan di dalam Perjanjian Jualan yang merangkumi Harga Belian Koperasi di dalam Perjanjian ini dan margin keuntungan dengan pembayaran secara tangguh tertakluk kepada terma dan syarat di dalam Tawaran Kemudahan.";
	
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
    document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").disabled = true;
	document.getElementById("button7").disabled = true;
	document.getElementById("button4a").disabled = true;
	document.getElementById("button8").hidden = false;
	document.getElementById("button9").hidden = false;
	document.getElementById("img11").hidden = false;
	document.getElementById("button22").hidden = false;
	document.getElementById("button23").hidden = false;

}
function myFunction6() {
	
	document.getElementById("demo6").innerHTML = "5. Perlantikan Wakil.";
	document.getElementById("demo6a").innerHTML = "Saya dengan ini telah membaca Surat Wakalah Agensi seperti yang tertera di dalam surat tersebut." ;
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
    document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").disabled = true;
	document.getElementById("button7").disabled = true;
	document.getElementById("button8").disabled = true;
	document.getElementById("button9").disabled = true;
	document.getElementById("button22").disabled = true;
	document.getElementById("button23").disabled = true;
	document.getElementById("button4a").disabled = true;
	document.getElementById("button6a").hidden = false;
	document.getElementById("button6b").hidden = false;
	document.getElementById("buttonagensi").hidden = false;
	document.getElementById("img4").hidden = false;


}
function myFunction7() {
	
 	 document.getElementById("demo7").innerHTML = "(a). Setelah pemeteraian Kontrak Jualan Murabahah ini disempurnakan, liabiliti dan hak pemilikan bermanfaat terhadap komoditi hendaklah dengan segera diserahkan kepada saya.";
	document.getElementById("demo7a").innerHTML = "(b) Saya dengan ini secara muktamadnya melantik Koperasi untuk menjadi wakil saya untuk menjual komoditi yang dinyatakan di dalam Tawaran Komoditi (Komoditi) kepada mana mana pembeli pihak ketiga yang difikirkan sesuai oleh Koperasi.";
	
	document.getElementById("demo7b").innerHTML = "(c) Koperasi hendaklah menyimpan sijil yang berkaitan dengan komoditi dalam jagaannya sebagai pemegang amanah bagi kepentingan saya untuk tujuan penjualan komoditi seterusnya kepada peniaga komoditi sepertimana yang dinyatakan di dalam Surat Perlantikan Wakil. Saya mempunyai hak untuk memeriksa perakuan tersebut di premis Koperasi atau memohon Koperasi untuk memberikan satu salinan sijil tersebut kepada saya melalui permohonan bertulis kepada Koperasi.";
	
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
    document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").disabled = true;
	document.getElementById("button7").disabled = true;
	document.getElementById("button8").disabled = true;
	document.getElementById("button9").disabled = true;
	document.getElementById("button4a").disabled = true;
	document.getElementById("button6a").disabled = true;
	document.getElementById("img12").hidden = false;
	document.getElementById("button10").hidden = false;
	document.getElementById("button11").hidden = false;
}

function myFunction8() {
	
 	document.getElementById("demo7A").innerHTML = "Saya dengan ini telah membaca Surat Wakalah Agensi serta bersetuju di atas pembelian di dalam surat tersebut." ;
	
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
    document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button4a").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").disabled = true;
	document.getElementById("button7").disabled = true;
	document.getElementById("button8").disabled = true;
	document.getElementById("button9").disabled = true;
	document.getElementById("button10").disabled = true;
	document.getElementById("button11").disabled = true;
	document.getElementById("button5A").disabled = true;
}

function myFunction9() {
	
 	 document.getElementById("demo9").innerHTML = "6. Saya bersetuju menjual komoditi itu kepada pihak pembeli ketiga dengan mewakilkan Koperasi pada harga kos  dan dengan itu mengkreditkan hasil jualan komoditi ke dalam akaun saya.";

	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
    document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").disabled = true;
	document.getElementById("button7").disabled = true;
	document.getElementById("button8").disabled = true;
	document.getElementById("button9").disabled = true;
	document.getElementById("button4a").disabled = true;
	document.getElementById("button6a").disabled = true;
	document.getElementById("button10").disabled = true;
	document.getElementById("button11").disabled = true;
	document.getElementById("button6b").disabled = true;
	document.getElementById("button22").disabled = true;
	document.getElementById("button23").disabled = true;
	document.getElementById("button12").hidden = false;
	document.getElementById("button13").hidden = false;
	document.getElementById("img5").hidden = false;
	document.getElementById("img9").hidden = false;
}
function myFunction10() {
	document.getElementById("demo10").innerHTML = "";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
    document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button4a").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").disabled = true;
	document.getElementById("button7").disabled = true;
	document.getElementById("button8").disabled = true;
	document.getElementById("button9").disabled = true;
	document.getElementById("button10").disabled = true;
	document.getElementById("button11").disabled = true;
	document.getElementById("button12").disabled = true;
	document.getElementById("button13").disabled = true;
	
	document.getElementById("button22").disabled = true;
	document.getElementById("button23").disabled = true;
	document.getElementById("agree").hidden = false;
	document.getElementById("SubmitForm").hidden = false;
	
	document.getElementById("img6").hidden = false;
}</script>';
