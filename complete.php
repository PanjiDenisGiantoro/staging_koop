<?php

/*********************************************************************************
 *          Project		:	Sistem e-Koperasi(ikoop)
 *          Filename		: 	complete.php
 *          Date 		: 	
 *********************************************************************************/
include("header.php");
$coopName = dlookup("setup", "name", "setupID=" . tosql(1, "Text"));
$email = dlookup("setup", "email", "setupID=" . tosql(1, "Text"));

?>
<h5 class="card-title">
  <img src="images/number1.png" width="17" height="17">&nbsp;ISI PROFIL&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i>&nbsp;
  <img src="images/number2.png" width="17" height="17">&nbsp;MUAT NAIK DOKUMEN&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i>&nbsp;
  <img src="images/number3.png" width="17" height="17">&nbsp;PEMBAYARAN&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i>&nbsp;
  <font class="text-primary"><img src="images/number4-primary.png" width="17" height="17">&nbsp;SELESAI</font>&nbsp;
</h5>

<div class="progress mb-3">
  <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-label="Animated striped example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
</div>
<?php

print '    
    <div align="center" class="mt-3">    
      <div class="card-body bg-soft-secondary">
        <img src="images/success.png" width="100" height="100" >    
           <p><h5 class="text-danger">!! SILA BUAT PEMBAYARAN PENDAFTARAN UNTUK PERCEPATKAN PROSES KELULUSAN !!</h5></p>
           
    </div>
      <br/>
        <center>
        <td class="textFont" align="center" colspan="3"><input type="button" class="btn btn-secondary waves-effect waves-light" onClick="window.location.href=\'index.php?vw=cara_byr\'" value="<<">
           <button type="button" class="btn btn-secondary waves-effect" onClick="window.location.href=\'?page=login&error=\'"><i class="fas fa-home"></i></button>
        </center>   
';

?>