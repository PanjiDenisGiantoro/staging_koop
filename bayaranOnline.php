<?php
/*********************************************************************************
*          Project    : iKOOP.com.my
*          Filename   :   bayaranTambahan.php
*          Date     :   15/06/2020
*********************************************************************************/
include ("header.php"); 
include("koperasiQry.php"); 
include ("forms.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" OR get_session("Cookie_koperasiID") <> $koperasiID) {
  print '<script>alert("'.$errPage.'"); parent.location.href = "index.php";</script>';
}

$sFileName    = "?vw=bayaranOnline&mn=9";
$sActionFileName= "testapi2.php?userID=".$userID."&amount=".$amount."&paymentName=".$paymentName."";
$title        = "Permohonan Penambahan Syer";

$strErrMsg = Array();

//--- Prepare state type
$stateList = Array();
$stateVal  = Array();
$GetState = ctGeneral("","J");
if ($GetState->RowCount() <> 0){
  while (!$GetState->EOF) {
    array_push ($stateList, $GetState->fields(name));
    array_push ($stateVal, $GetState->fields(ID));
    $GetState->MoveNext();
  }
} 

$name = dlookup("setup", "name", "setupID=" . tosql(1, "Text"));
if (preg_match("/\((.*?)\)/", $name, $matches)) {
    $abbreviation = $matches[1];
}

print '
<table cellspacing="0" cellpadding="0">
  <tr>
    <td > <b class="maroonText">LANGKAH-LANGKAH UNTUK PENAMBAHAN SYER<b></td>
  </tr>
  <tr>
    <td><p><p>Bagi  membolehkan kami memberikan perkhidmatan yang terbaik kepada anggota,  sila pastikan langkah-langkah berikut diikuti, iaitu :-</p>
        </p>
      <p> 1. Sentiasa mengemaskini <b>PROFIL ANGGOTA</b>, terutamanya alamat emel anggota bagi memudahkan penghantaran resit secara automatik kepada emel anggota.<br>
        </p>
      
      <p> 2. Pastikan juga nombor telefon telah dikemaskini dengan meletakkan <b>angka 6 dihadapan nombor telefon.</b><br>
        </p>
      
      <p> 3. Penambahan minima yang boleh dibuat adalah sebanyak <b>RM 100.00</b>. Manakala penambahan maksima yang boleh dibuat adalah sebanyak RM20,000 <br>
        </p>
      
      <p> 4. Caj transaksi sebanyak <b>RM 1.00</b> akan dikenakan untuk setiap transaksi yang dilakukan.<br>
        </p>
      
      <p> 5. Setelah bayaran dibuat, penyata anggota akan dikemaskini dalam dua hari bekerja. Sila semak di menu pengguna setelah dua hari membuat pembayaran. Sekiranya penyata tidak dikemaskini, sila hubungi pihak <b>'.$abbreviation.'</b> untuk tindakan lanjut.</p>
      
      <p> 6. Selepas membuat bayaran, anggota perlukan mengklik butang <i style="font-size: 14px;"><u><b>"Return To Merchant"</b></u></i> untuk memastikan pembayaran yang dibuat masuk ke dalam lejer anggota.</p>
      
      <p> 7. Bayaran yang dibuat melalui <b><i>Payment Gateway</i> SWITTLE </b>.</p>
      
      <p> 8. Segala kesulitan amat dikesali.</p>

      <p ><b>Sila <u style="color: red; font-size: 16px;">UNBLOCK POPUP</u> di Google Chrome atau Safari ataupun <i>browser</i> yang digunakan anggota terlebih dahulu.</b></p>
      <p></p></td>
  </tr>
</table>
';

$a = 0;
$FormLabel[$a]    = "* Nombor Anggota";
$FormElement[$a]  = "userID";
$FormType[$a]     = "hiddentext";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array(CheckBlank);
$FormSize[$a]     = "10";
$FormLength[$a]   = "10";

$a++;
$FormLabel[$a]    = "Nama Anggota";
$FormElement[$a]  = "name";
$FormType[$a]     = "hiddentext";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array();
$FormSize[$a]     = "35";
$FormLength[$a]   = "50";

$a++;
$FormLabel[$a]    = "Kad Pengenalan";
$FormElement[$a]  = "newIC";
$FormType[$a]     = "hiddentext";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array();
$FormSize[$a]     = "12";
$FormLength[$a]   = "12";

$a++;
$FormLabel[$a]    = "Nombor Telefon (Tiada '-')";
$FormElement[$a]  = "mobileNo";
$FormType[$a]     = "text";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array(CheckBlank);
$FormSize[$a]     = "20";
$FormLength[$a]   = "12";

$a++;
$FormLabel[$a]    = "Emel Anggota";
$FormElement[$a]  = "email";
$FormType[$a]     = "hiddentext";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array(CheckBlank);
$FormSize[$a]     = "30";
$FormLength[$a]   = "20";

$a++;
$FormLabel[$a]    = "Pilihan Bayaran";
$FormElement[$a]  = "paymentName";
$FormType[$a]     = "radio";
$FormData[$a]     = array('Syer');
$FormDataValue[$a]= array('1596');
$FormCheck[$a]    = array();
$FormSize[$a]     = "1";
$FormLength[$a]   = "1";

$a++;
$FormLabel[$a]    = "Jumlah (RM)";
$FormElement[$a]  = "amount";
$FormType[$a]     = "text";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array(CheckBlank);
$FormSize[$a]     = "10";
$FormLength[$a]   = "10";


$userID = get_session('Cookie_userID');
$strMember = "SELECT a.*,b.* FROM users a, userdetails b WHERE a.userID = '".$userID."' AND a.userID = b.userID";
$GetMember = &$conn->Execute($strMember);

if ($SubmitForm <> "") {
  //--- Begin : Call function FormValidation ---  
  for ($i = 0; $i < count($FormLabel); $i++) {
    for($j=0 ; $j < count($FormCheck[$i]); $j++) {
      FormValidation ($FormLabel[$i], 
              $FormElement[$i], 
              $$FormElement[$i],
              $FormCheck[$i][$j],
              $i);
    }
  } 

  if ($mobileNo) {
  if (!ereg ("(6[0-9]{3})([0-9]{3})([0-9]{4})", $mobileNo, $regs)) {
    array_push ($strErrMsg, "mobileNo");
    print '- <font class=redText>* Masukkan Nombor Kod Negara [6][0112223333].</font><br />';
  }
  }

  if ($amount > 20001) {
    array_push ($strErrMsg, "amount");
    print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
            <strong>Amaun maksimum yang dibenarkan ialah RM20,000.</strong> 
            </div>';
    }


    if (count($strErrMsg) == "0") {

    $sSQL = "";
    $sWhere = "";       
    $sWhere = "userID=" . tosql($userID, "Text");
    $sWhere = " WHERE (" . $sWhere . ")";   
    $sSQL = "UPDATE users SET ".
            " email=".tosql($email, "Text").
    $sSQL = $sSQL . $sWhere;
    $rs = &$conn->Execute($sSQL);

    $sSQL = "";
    $sWhere = "";       
    $sWhere = "userID=" . tosql($userID, "Text");
    $sWhere = " WHERE (" . $sWhere . ")";   
    $sSQL = "UPDATE userdetails SET ".
            " mobileNo=".tosql($mobileNo, "Text").
    $sSQL = $sSQL . $sWhere;
    $rs = &$conn->Execute($sSQL);


  print '<script>
          alert ("Sila Tunggu Sebentar Sementara Untuk Ke Paparan SWITTLE");
          window.open ("'.$sActionFileName.'");
        </script>';
  }
} 

?> 
<div class="table-responsive">
<div style="width: 500px; text-align:left">

<form name="MyForm" action=<?php print $sFileName;?> method="POST">
<table class="table lightgrey" border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
<tr>
<td class="borderallteal" align="left" valign="middle"><div class="headerteal"><b>Permohonan Penambahan Syer</b></div></td>
</tr>
<tr>
<td class="borderleftrightbottomteal">
    
    <table class="table table-light" border="0" cellspacing="6" cellpadding="0" width="100%" align="center">
    
    <?php


    //--- Begin : Looping to display label -------------------------------------------------------------
    for ($i = 0; $i < count($FormLabel); $i++) {
      print '<tr valign="top"><td align="right">'.$FormLabel[$i].'</td>';
      if (in_array($FormElement[$i], $strErrMsg))
        print '<td class="errdata">';
      else
        print '<td>';
      //--- Begin : Call function FormEntry ---------------------------------------------------------  
      $strFormValue = tohtml($GetMember->fields($FormElement[$i])); 
      FormEntry($FormLabel[$i], 
            $FormElement[$i], 
            $FormType[$i],
            $strFormValue,
            $FormData[$i],
            $FormDataValue[$i],
            $FormSize[$i],
            $FormLength[$i]);

      //--- End   : Call function FormEntry ---------------------------------------------------------  
        ?>&nbsp;</td></tr><?php
    }

    if($userID) {
    ?>
    <tr>
    <td colspan="2" align="center">
    <div>&nbsp;</div>
    <input type="Submit" class="btn btn-primary w-md waves-effect waves-light" name="SubmitForm" value="Hantar">
    <div>&nbsp;</div>
    </td>
    </tr>
    <?}?>
    </table>
</td>
</tr>
</table>
</form>
</div></div>
<?
include("footer.php");  
?>
