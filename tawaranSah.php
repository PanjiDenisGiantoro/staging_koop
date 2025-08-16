<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *          Date 		: 	
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$sFileName		= "tawaranSah.php";
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
$FormLabel[$a]   	= "No KP Baru";
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
$FormLabel[$a]   	= "Nombor Rujukan";
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
				AND a.loanID =" . $ID . "";
$GetLoan = &$conn->Execute($strLoan);
$loanAmtTotal = number_format($GetLoan->fields(totalLoan), 2);
$noloan = $GetLoan->fields(no_sijil);
$loanAmtLo = $GetLoan->fields(loanAmt);
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
			", statusT=" . 1 .
			", stat_agree=" . 2 .
			", opsyen_sah=" . tosql($opsyen_sah, "Number") .
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
	if ($i == 1) print '<tr><td class=Header colspan=4>i. PENGESAHAN ON-LINE DOKUMEN PEMBIAYAAN DAN PERMOHONAN UNTUK MENGELUARKAN PEMBIAYAAN</td></tr>';

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
    <p>1. Saya seperti nama di atas dengan ini mengesahkan dokumen pembiayaan telah diterima dan telah dilengkapkan dengan sempurna.</p>
    <table width="100%" border="0" cellpadding="0">
    <tr><td align="left">
	<input type="button" id="button1" value="YA" onclick="myFunction2()"  />
	<img src="images/sym-tick-red-bkrm-01.gif" id="img0" width="20" height="20" hidden="enabled">
    <input type="button" id="button2" value="TIDAK" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};" />


<p id="demo2ai"></p></p></td></tr></table>
	<br> 
	
	<p id="demo2"></p>
    <table width="100%" border="0" cellpadding="0">
    <tr><td align="left">
	<input type="button" id="button3" value="YA" hidden="enabled" onclick="myFunction3()"  />
	<img src="images/sym-tick-red-bkrm-01.gif" id="img1" width="20" height="20" hidden="enabled">
    <input type="button" id="button4" value="TIDAK" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  />
		  
</td></tr></table>

	<p id="demo3ai"></p>
    <p id="demo3a"></p>
    <p id="demo3b"></p>
    <p id="demo3c"></p>
    <table width="100%" border="0" cellpadding="0">
    <tr>
    <td width="7%" align="center" valign="top"><input type="radio" hidden="enabled" name="opsyen_sah" id="check3a" value ="2" onclick="myFunction3Ra();"></td>
	<td id="demo3d" colspan="5"></td>
    </tr>
    <tr>
        <td align="right">&nbsp;</td>
        <td id="demo3e" align="left"></td>
        <td width="18%">&nbsp;</td>
        <td width="18%">&nbsp;</td>
        <td width="18%">&nbsp;</td>
        <td width="18%">&nbsp;</td>
      </tr>
      <tr>
        <td align="center" valign="top"><input type="radio" name="opsyen_sah" hidden="enabled" value ="1" id="check3b" onclick="myFunction3R();"></td>
        <td id="demo3f" colspan="5" align="left"></td>
      </tr>
    </table>

    	<table width="100%" border="0" cellpadding="0">
      	<tr><td align="left">
	  	<p id="demosaham"> </p>
        <input type="button" id="button5" value="YA" hidden="enabled" onclick="myFunction4()"  />
		<img src="images/sym-tick-red-bkrm-01.gif" id="img2" width="20" height="20" hidden="enabled">
</td></tr></table>
	

	
   	<p id="demo4"></p>
	<p id="demo4b"></p>
			
    <table width="100%" border="0" cellpadding="0">
    <tr><td align="left">
	<input type="button" id="button6" value="YA" hidden="enabled" onclick="myFunction5()"  />
	<img src="images/sym-tick-red-bkrm-01.gif" id="img3" width="20" height="20" hidden="enabled">
    <input type="button" id="button7" value="TIDAK" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  /></td>
      </tr>
	  <tr><td></td></tr>
	  <tr><td></td></tr>
	  <tr>
	  <td><p id="demo5"></p>
	    </td>
      </tr>
    </table>
	
	
	<p id="demo5a"></p>
	<table width="100%" border="0" cellpadding="0">
      <tr><td align="left">
		  <input type="button" id="button8" value="YA" hidden="enabled" onclick="myFunction6()"  />
		   <img src="images/sym-tick-red-bkrm-01.gif" id="img4" width="20" height="20" hidden="enabled">
          <input type="button" id="button9" value="TIDAK" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  />
	</td>
      </tr>
    </table>

	<p id="demo6"></p>
    <table width="100%" border="0" cellpadding="0">
      <tr><td align="left">
		  <input type="button" id="button10" value="YA" hidden="enabled" onclick="myFunction10()"  />
		   <img src="images/sym-tick-red-bkrm-01.gif" id="img5" width="20" height="20" hidden="enabled">
          <input type="button" id="button11" value="TIDAK" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  /></td>
</tr></table>';

print '<tr>
		<td class="data" align="left" colspan="4"><input type="checkbox" class="form-check-input" name="agree" id="agree" hidden ="enabled">Saya dengan ini bersetuju dengan pengesahan di atas.</td>
		<p id="demo10"></p>
		
		</tr>';
print '<tr><td colspan=4 align=left class=Data>
			<input type="hidden" name="ID" value="' . $ID . '">
			<input type=button name=kembali class="but" value=Tutup onClick="window.close();">
			<input type="button" id="SubmitForm" hidden="enabled" class="but" name="SubmitForm" value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');">
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
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
	          //alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
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
	 document.getElementById("demo2ai").innerHTML = "* dengan ini Pihak Koperasi bersetuju untuk menjual asset/komoditi tersebut mengikut syarat yang telah dipersetujui.";
    document.getElementById("demo2").innerHTML = "2. BAHAWA dengan ini saya bersetuju membeli &quot;Barang Jualan&quot; tersebut yang telah dipersetujui seperti yang dilampirkan sebelum ini secara harga tangguh dan mengikuti syarat yang telah dipersetujui.";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
	document.getElementById("button3").hidden = false;
	document.getElementById("button4").hidden = false;
	document.getElementById("img0").hidden = false;
}


function myFunction3() {

	document.getElementById("demo3a").innerHTML = "3. Pengesahan Opsyen";
	document.getElementById("demo3b").innerHTML = "Saya dengan ini memilih opsyen terhadap aset tersebut yang dinyatakan sebelum ini untuk :";
	document.getElementById("demo3c").innerHTML = "(sila tick yang mana berkenaan)";
	document.getElementById("demo3d").innerHTML = "Mengambil pemilikan aset/komoditi tersebut secara fizikal atau menjual kepada pihak-pihak lain dengan segala kos berkaitan ditanggung oleh saya sendiri. Saya dengan ini melepaskan Koperasi [NAMA KOPERASI] daripada segala tanggungan dan mengaku janji untuk menanggungrugi ke semua kos dan perbelanjaan atau kerosakan yang Koperasi mungkin alami atau tanggung berikutan pilihan ini.";
	document.getElementById("demo3e").innerHTML = "<strong>ATAU</strong>";
	document.getElementById("demo3f").innerHTML = "Setuju menjual aset/komoditi tersebut kepada Koperasi [NAMA KOPERASI] mengikut syarat-syarat yang ditetapkan di dalam perjanjian belian.";

	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
	document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("check3a").hidden = false;
	document.getElementById("check3b").hidden = false;
	document.getElementById("img1").hidden = false;

}

function myFunction3Ra() {

    document.getElementById("button5").hidden = false;
	document.getElementById("demosaham").innerHTML = "<b>Tawaran Pembelian Bagi Syer Siarharga PIGTF Public Mutual (1 Unit Syer = RM20.00 iaitu Bersamaan : RM ' . $loanAmt . '/20 Perunit = ' . $totalshare . ' unit)</b>";
	document.getElementById("demosaham").hidden = false;
	
}
function myFunction3R() {
	document.getElementById("button5").hidden = false;
	document.getElementById("demosaham").hidden = true;
}

function myFunction4() {
	
   document.getElementById("demo4").innerHTML = "4. Tawaran Pembelian.";
	document.getElementById("demo4b").innerHTML = "BAHAWA dengan ini saya bersetuju menjual  &quot;Barang Jualan&quot; tersebut yang telah dipersetujui seperti di lampirkan sebelum ini secara harga tunai dan mengikut syarat yang telah dipersetujui.";
	
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
	document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").hidden = false;
	document.getElementById("button7").hidden = false;	
	document.getElementById("img2").hidden = false;
}

function myFunction5() {
	
	document.getElementById("demo5").innerHTML = "* dengan ini Pihak Koperasi bersetuju untuk membeli asset/komoditi tersebut mengikut syarat yang telah dipersetujui.";
	document.getElementById("demo5a").innerHTML = "5. Saya bersetuju dengan ke semua terma dan syarat lain berkaitan belian aset  oleh Koperasi." ;
	
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
    document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").disabled = true;
	document.getElementById("button7").disabled = true;
	document.getElementById("button8").hidden = false;
	document.getElementById("button9").hidden = false;
	document.getElementById("img3").hidden = false;
}
function myFunction6() {
	document.getElementById("demo6").innerHTML = "6. Saya memohon untuk dikreditkan pembiayaan sebanyak RM ' . $loanAmt . '";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
    document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").disabled = true;
	document.getElementById("button7").disabled = true;
	document.getElementById("button8").disabled = true;
	document.getElementById("button9").disabled = true;
	document.getElementById("button10").hidden = false;
	document.getElementById("button11").hidden = false;
	document.getElementById("img4").hidden = false;
}
function myFunction10() {
	document.getElementById("demo10").innerHTML = "";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
    document.getElementById("button3").disabled = true;
	document.getElementById("button4").disabled = true;
	document.getElementById("button5").disabled = true;
	document.getElementById("button6").disabled = true;
	document.getElementById("button7").disabled = true;
	document.getElementById("button8").disabled = true;
	document.getElementById("button9").disabled = true;
	document.getElementById("button10").disabled = true;
	document.getElementById("button11").disabled = true;
	document.getElementById("agree").hidden = false;
	document.getElementById("SubmitForm").hidden = false;
	document.getElementById("img5").hidden = false;
}</script>';
