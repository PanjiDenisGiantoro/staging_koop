<?php
/*********************************************************************************
*          Project		:	KPF2 Modul Pelaburan
*          Filename		: 	investVoucherReports.php
*          Date 		: 	28/02/2024
*********************************************************************************/
include("header.php");	

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName = "?vw=investorReports&mn=$mn";
$sFileRef  = "?vw=investorReports&mn=$mn";
// $title     = $lapList[array_search($cat,$lapVal)];
$title     = "LAPORAN PELABURAN";
?>

<h5 class="card-title"><? print strtoupper($title);?></h5>
<div style="width: 100%; text-align:left">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">


<div>&nbsp;</div>
	<tr>
		<td class="Label" valign="top" colspan="3">
		<h6 class="card-subtitle"><u><b>BAUCER PELABURAN</b></u></h6>
		<li id="print" class="textFont"><a href="#" onclick="selectBaucerPelaburan('rptInvest3')">Senarai Baucer Pelaburan</a></li>
	</td>
	</tr>

</table>
<?php
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
			rpt == "rptmbrBersara" ||rpt == "rptA19" ||rpt == "rptA20" || rpt == "rptDaftarAng" || rpt == "rptPembiayaanT" || rpt == "rptJumPotonganThn" ||
			rpt == "rptElaunBank" || rpt == "rptElaunPokok")  {
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
		window.open(url ,"pop","top=100,left=100,width=800,height=200,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
		
	}

	function selectPembiayaanAD(rpt) {
		s = "selDateOptAD.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=0,width=1200,height=200,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
		
	}
	
	function selectPembiayaan1(rpt) {
		s = "selDateOptN.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
		
	}	  

	function selectPembiayaan2(rpt) {
		url = "selYear2.php?rpt="+rpt+"";
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
	
	} 

	function selectSaham(rpt) {
		s = "selDateOpt.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}
    
    function selectSyarikat(rpt) {
        s = "selInvest.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	
    }

    function selectBaucerPelaburan(rpt) {
        s = "selInvestInvoisBaucerResit.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	
    }
	
	function selectUrusniaga(rpt) {
		if (rpt == "rptD1") {
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
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

