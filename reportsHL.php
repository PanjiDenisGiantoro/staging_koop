<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	reports.php
*          Date 		: 	29/03/2004
*********************************************************************************/
include("header.php");	

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> $koperasiID) {
	$temp = '<script>alert("'.$errPage.'"); parent.location.href = "index.php";</script>';
	print $temp;
}

$sFileName = 'reportsHL.php';
$sFileRef  = 'reportsHL.php';
$title     = 'LAPORAN HUTANG LAPUK';

?>
<div class="maroon" align="left"><b>&nbsp;<? print strtoupper($title);?></b></div>
<div style="width: 100%; text-align:left">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onClick="selectUrusniaga('rpthutanglapuk')">Tunggakan Potongan Gaji</a>
	</tr>

</table>
<?
include("footer.php");	
print '
<script>
	function selectDividen(rpt) {
		url = "selYear.php?rpt="+rpt+"&id=ALL";
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}
	
	function selectAsas(code) {
		window.open("rptAsas.php?code="+code ,"pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");		
	}	  

	function selectAnggota(rpt) {
		if (rpt == "rptA4" || rpt == "rptA5" || rpt == "rptA6" || rpt == "rptA7" || rpt == "rptA8" || rpt == "rptA9" ||
			rpt == "rptA10" || rpt == "rptA11" || rpt == "rptA12a" || rpt == "rptA12" || rpt == "rptA13" || rpt == "rptA14" || rpt == "rptA15" ||
			rpt == "rptmbrBersara" || rpt == "rptDaftarAng")  {
			window.open(rpt+".php" ,"pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");					
		} else {
			s = "selDateOpt.php";
			url = s + "?rpt=" + rpt;
			window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
		}
	}	  

	function selectPembiayaan(rpt) {
		s = "selDateOpt.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	  

	function selectSaham(rpt) {
		s = "selDateOpt.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	  	
	
	function selectUrusniaga(rpt) {
		if (rpt == "rptBThn") {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}else if (rpt == "rptBDivTgkk" ) {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}else if (rpt == "rptSUMDIVALL" ){
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}else if (rpt == "rptSUMDIVT" ){
			url = "selYear.php?rpt="+rpt+"&id=ALL";a
		}else{	url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		}
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
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
     	} else if (rpt=="rptSenaraiBakiAkhirPem"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else if (rpt=="rptSenaraiBakiAkhirSBP"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else if (rpt=="rptPecahanPinYuran"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}
		
		window.open(url ,"pop","top=100,left=100,width=750,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	  
	
	function selectHotList(rpt) {
	
		if (rpt=="hotYuran" || rpt=="hotPembiayaan") {
			url = "selTempoh.php?rpt="+rpt;
			window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");					
		} else {
			url = rpt+".php";
			window.open (url, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
		}
			

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
		window.open(url ,"pop","top=100,left=100,width=650,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	
</script>';
?>
