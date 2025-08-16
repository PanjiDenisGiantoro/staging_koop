<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	subscription.php
*          Date 		: 	04/12/2018
*********************************************************************************/

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 50;
if (!isset($q))			$q="";
if (!isset($by))		$by="1";
if (!isset($filter))	$filter="0";
if (!isset($dept))		$dept="";

include("header.php");	
include("koperasiQry.php");	
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>parent.location.href = "index.php";</script>';
}
$sFileName = "?vw=subscription&mn=$mn";
$sFileRef  = "?vw=adminEdit&mn=mn";
$title     = "Status Langganan";

print '
<form name="MyForm" action=' .$sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk" value="<?=$pk?>">
<input type="hidden" name="filter" value="'.$filter.'">
<div class="table-responsive">
<table border="0" cellspacing="3" cellpadding="3" width="100%" align="center">
<h5 class="card-title">' . strtoupper($title) . '<h5>';

$name = dlookup("setup", "name", "setupID=" . tosql(1, "Text"));
$result = preg_replace('/\s*\(.*\)$/', '', $name);
?>


<i class="mdi mdi-credit-card-check text-secondary"></i> MAKLUMAT LANGGANAN
			<div>&nbsp;</div>
				<div style="display: table; width: 80%; border-collapse: collapse;">
					<div style="display: table-row;">
						<div style="display: table-cell; padding: 8px"><i class="mdi mdi-office-building"></i> Nama Koperasi</div>
						<div style="display: table-cell; padding: 8px;">: <b><?print $result;?></b></div>
					</div>
					<div style="display: table-row;">
						<div style="display: table-cell; padding: 8px"><i class="ion ion-ios-settings"></i> Jenis Perkhidmatan</div>
						<div style="display: table-cell; padding: 8px">: <b>Sistem Pengurusan Koperasi Bersepadu</b></div>
					</div>
					<div style="display: table-row;">
						<div style="display: table-cell; padding: 8px"><i class="ion ion-ios-options"></i> Kategori Langganan</div>
						<div style="display: table-cell; padding: 8px">: <b>Platinum</b></div>
					</div>
					<div style="display: table-row;">
						<div style="display: table-cell; padding: 8px"><i class="mdi mdi-calendar-blank"></i> Tarikh Mula Langganan</div>
						<div style="display: table-cell; padding: 8px">: <b></b></div>
					</div>
					<div style="display: table-row;">
						<div style="display: table-cell; padding: 8px"><i class="mdi mdi-calendar"></i> Tarikh Tamat Langganan</div>
						<div style="display: table-cell; padding: 8px">: <b></b></div>
					</div>
					<div style="display: table-row;">
						<div style="display: table-cell; padding: 8px"><i class="dripicons dripicons-clock"></i> Tempoh Langganan</div>
						<div style="display: table-cell; padding: 8px">: <b></b></div>
					</div>
					<div style="display: table-row;">
						<div style="display: table-cell; padding: 8px"><i class="bx bx-user-pin"></i> Pegawai Penyeliaan Program MOF</div>
						<div style="display: table-cell; padding: 8px">: <b>Faiz Bin Ahmad Yatim</b></div>
					</div>
					<div style="display: table-row;">
						<div style="display: table-cell; padding: 8px"><i class="mdi mdi-file-document"></i> Dokumen Persetujuan</div>
						<div style="display: table-cell; padding: 8px">: <b><a href="sst.pdf">Surat Setuju Terima</a></b></div>
					</div>
				</div>

<!-- <h5 class="mt-5">PAKEJ TERSEDIA</h5>
<center><img src="images/pricing1.png" height="500px"/></center>
<div>&nbsp;</div>
<center><img src="images/pricing2.png" height="500px"/></center> -->
<?php
print ' 
</table>
</div>
</form>';

include("footer.php");	
