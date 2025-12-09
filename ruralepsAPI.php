<?php 
ob_start();
include("header.php");
include("koperasiQry.php");

$sFileName    = "bayaranOnline.php";

  date_default_timezone_set("Asia/Jakarta"); 
  $date = date("Y-m-d H:i:s"); 
  $today = date("Y-m-d"); 

  $memberID   = $_GET['userID'];
  $amount     = $_GET['amount'];
  $paymentID  = $_GET['paymentName'];
  $paymentName = dlookup("general", "name", "ID=" . $paymentID);

$Name = dlookup("users", "name", "userID=" . $memberID);
$ICno = dlookup("userdetails", "newIC", "userID=" . $memberID);
$email = dlookup("users", "email", "userID=" . $memberID);
$mobileNo = dlookup("userdetails", "mobileNo", "userID=" . $memberID);
$address = dlookup("userdetails", "address", "userID=" . $memberID);
$caj = 1;
$redirectlink = 'https://pembiayaan.ruralcapital.com.my/getApi.php ';

$amountcaj = ($amount + $caj);
$merchantID = 'UAT2001004';


// $cajamaun = number_format($amountcaj,2);

$ch = curl_init();

// $url = "http://test-api.swipego.io/api/payment-links";

$url ="https://gerbang-uat.orijin.my/in";

$data = array(
  'email' => "".$email."",
  'phone_no' => "".$mobileNo."",
  'currency' => 'MYR',
  'amount' => "".$amountcaj."",
  'title' => "".$paymentName."",
  'reference' => "".$memberID."",
  'reference_2' => "".$paymentID."",
  'redirect_url' => "".$redirectlink.""
);

$headers = array(
     "Accept:application/json",
    "Content-Type: application/json",
    "Authorization: Bearer 1@@%csjnd!n9xMNvLB8S2",
);

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0); 
curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data,true)); 

$output = curl_exec($ch);

if(curl_errno($ch))
  {
    echo 'Curl error: '.curl_error($ch);
  }

$response   = json_decode($output,true);
$redirect   =$response["data"]["payment_url"];
$bills_no   =$response["data"]["_id"];

print_r($bills_no);

if(!$response){
  echo "Transaksi tidak dapat dijalankan, sila tunggu sebentar dan cuba kembali selepas ini. Maaf atas kesulitan dan sekian terima kasih.";
}else{

    $sSQL = "";
    $sSQL = "";
    $sSQL = "INSERT INTO resitonline (" . 
          "no_resit, " .
          "tarikh_resit, " .
          "bayar_kod, " .
          "amount, " .
          "bayar_nama, " .
          "createdDate, " .
          "createdBy, " .
          "updatedDate, " .
          "updatedBy, " .
          "statusRP) " .
                " VALUES (".
          "'". $bills_no . "', ".
          "'". $date . "', ".
          "'". $paymentID . "', ".
          "'". $amount . "', ".
          "'". $memberID . "', ".
          "'". $date . "', ".
          "'". $memberID . "', ".
          "'". $updatedDate . "', ".
          "'". $updatedBy . "', ".
          "'". 0 . "') ";           
  $rs = &$conn->Execute($sSQL);

  print '<script>
          alert ("Ia akan pergi ke muka seterusnya.");';
         header("Location: ".$redirect);
  print'</script>';
}

curl_close($ch);
ob_end_flush();
?>