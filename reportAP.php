<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*********************************************************************************/
$title     =  "Laporan Advance Payment";
$sFileName = 'reportAP.php'; 

?>
<form name="MyForm" action=' .$sFileName . ' method="post">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<h5 class="card-title"><? print strtoupper($title);?></h5>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
    <tr>
        <td class="Label" valign="top">
            <h6 class="card-subtitle mt-3"><u>Laporan Advance Payment</u></h6>
            <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB5')">Permohonan Advance Payment</a>
            <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB6')">Kelulusan Advance Payment</a>
            <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB7')">Permohonan Advance Payment Yang Ditolak</a>
            <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan('rptB6A')">Keseluruhan Advance Payment</a>
            <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata('rptbank_baucerAP')">Penyata Laporan Baucer</a></li>
        </td>
    </tr>
</table>

<?php
print '
<script>
    function selectPembiayaan(rpt) {
		s = "selDateOpt.php";
		url = s + "?rpt=" + rpt;
		window.open(url ,"pop","top=100,left=100,width=800,height=200,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
		
	}

    function selectPenyata(rpt) {
		if (rpt=="rptbank_baucerAP"){
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		}
		 else {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}
		
		window.open(url ,"pop","top=100,left=100,width=750,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	  

</script>';