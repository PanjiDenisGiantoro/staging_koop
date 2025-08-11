<?php

/*********************************************************************************
 *          Project    : Sistem e-Koperasi(ikoop)
 *          Filename   :   cara_byr.php
 *          Date     :   
 *********************************************************************************/
include("header.php");
$newIC    = $_GET["ic"];
$mobileNo = $_GET["mobileNo"];
$email    = $_GET["email"];

$coopName = dlookup("setup", "name", "setupID=" . tosql(1, "Text"));
$email = dlookup("setup", "email", "setupID=" . tosql(1, "Text"));
$kandungan = dlookup("syarat", "kandungan", "ID=" . tosql(998, "Text"));
?>
<h5 class="card-title">
  <img src="images/number1.png" width="17" height="17">&nbsp;ISI PROFIL&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i>&nbsp;
  <img src="images/number2.png" width="17" height="17">&nbsp;MUAT NAIK DOKUMEN&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i>&nbsp;
  <font class="text-primary"><img src="images/number3-primary.png" width="17" height="17">&nbsp;PEMBAYARAN&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i></font>&nbsp;
  <img src="images/number4.png" width="17" height="17">&nbsp;SELESAI
</h5>

<div class="progress mb-3">
  <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-label="Animated striped example" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
</div>
<?php

print '
        
    <div>
    <table class="lightgrey" border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td align="center" valign="middle"><h2 class="text-primary"><b>!! TAHNIAH !!</b></h2></td>
      </tr>
      <tr>
        <td class="borderleftrightbottomblue">
        <table border="0" cellpadding="0" cellspacing="6" width="100%" align="center">
          <tr>
           <br>
            <div align="center"><h5 class="text-primary">PERMOHONAN ANDA UNTUK MENJADI ANGGOTA BERJAYA DIHANTAR</h5></div>
            <div align="center"><h5 class="text-danger">!! SILA BUAT PEMBAYARAN PENDAFTARAN UNTUK PERCEPATKAN PROSES KELULUSAN !!</h5></div><br/>';
print $kandungan;
print '
          <p> <h5 align="center"> BAYARAN YANG DIBUAT MELALUI PAYMENT GATEWAY SWIPEGO <b>(FINTECH WORLDWIDE SDN BHD) </b> DAN AKAN DIKREDITKAN KE DALAM AKAUN KOPERASI DALAM TEMPOH 24 JAM</h5></p>
          
            </td>
          </tr>
<tr><td align="center">
 <br><p align="center"><a title= "UNTUK PEMBAYARAN ONLINE" href="" target="_blank"rel="noopener"><strong><h4>BAYARAN ONLINE DISINI</h4></strong> </a></p>
           <br>

       </td></tr>   
        </table>
        </td>
      </tr>
    </table>
</div>
';

//https://app.swipego.io/payment/open-link/pay?id=63e87f3bbf6868022d068fe2
//https://test-api.swipego.io/payment/open-link/pay?id=634cf729c617b997f5009142