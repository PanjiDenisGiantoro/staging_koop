<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*********************************************************************************/
$title     = "Laporan Penghutang";
$sFileName = 'reportDebtor.php'; 

?>
<form name="MyForm" action=' .$sFileName . ' method="post">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<h5 class="card-title"><? print strtoupper($title);?></h5>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
    <tr>
        <td class="Label" valign="top">
            <h6 class="card-subtitle mt-3"><u>Laporan Statement Account</u></h6>
            <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan2('ACCinvoisAll')">Statement of Account (Penghutang)</a>
            <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan3('ACCdebtorSOA')">Statement of Account (Penghutang) Kelompok</a>
            <h6 class="card-subtitle mt-3"><u>Laporan Aging</u></h6>
            <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan4('rptAgingDebtor')">Aging (Penghutang)</a>
            <li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPembiayaan3('rptAgingDebtorAll')">Aging (Penghutang) Kelompok</a>
        </td>
    </tr>
</table>

<?php
print '
<script>
    function selectPembiayaan2(rpt) {
        url = "selYear4.php?rpt="+rpt+"";
        window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
        
    } 

    function selectPembiayaan3(rpt) {
        url = "selYear6.php?rpt="+rpt+"";
        window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
        
    } 

    function selectPembiayaan4(rpt) {
        url = "selYear7.php?rpt="+rpt+"";
        window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");	
        
    } 
</script>';