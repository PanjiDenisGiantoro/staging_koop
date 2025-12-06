<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	reports.php
*          Date 		: 	29/03/2004
*********************************************************************************/
include("header.php");	

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'"); parent.location.href = "index.php";</script>';
}

$sFileName = "?vw=reportsDIV&mn=$mn";
$sFileRef  = "?vw=reportsDIV&mn=$mn";
$title     = 'LAPORAN DIVIDEN ANGGOTA';

?>
<h5 class="card-title"><b><? print strtoupper($title);?></h5>
<div class="card-group mt-3">
  <div class="card bg-soft-secondary">
    <center><img class="card-img-top mt-3" style="width: 3rem; height: 3rem;" src="images/dividends.png" alt="Picture is missing"></center>
    <div class="card-body">
      <h5 class="card-text" align="center">SYER/YURAN/SIMPANAN</h5>
	  <h4 class="card-text" align="center">2022</h4>
      <h2 class="card-text" align="center"><font color="black">RP&nbsp;0.00</font></h2>
      </div>
  </div><?php
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
			rpt == "rptmbrBersara" || rpt == "rptDaftarAng" || rpt == "rptJumAllDiv"  || rpt == "rptDivtest")  {
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
		}else if (rpt == "rptBThnAll" ){
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}else if (rpt == "rptBDivTgkkUP" ){
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}else if (rpt == "rptJumAllDiv" ){
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}else if (rpt == "rptDivTest" ){
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}else if (rpt == "rptBThnBer" ){
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}else{	url = "selYear.php?rpt="+rpt+"&id=ALL";
		}
		window.open(url ,"pop","top=100,left=100,width=500,height=110,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
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
			url = "selYearPem.php?rpt="+rpt+"&id=ALL";
		} else if (rpt == "rptDivTest" ){
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		} else if (rpt=="rptPecahanPinWajib"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}
		
		window.open(url ,"pop","top=100,left=100,width=750,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	  
	
	function selectHotList(rpt) {
	
		if (rpt=="hotWajib" || rpt=="hotPembiayaan") {
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
