<?php 
ob_start();
include("header.php");
include("koperasiQry.php");

$sFileName    = "komoditi_list.php";

  date_default_timezone_set("Asia/Jakarta"); 
  $date = date("Y-m-d H:i:s"); 
  $today = date("Y-m-d"); 

  $userID     = $_GET['userID'];
  $amount     = $_GET['amount'];
  $loanid     = $_GET['loanid'];

$Name         = dlookup("users", "name", "userID=" . $userID);
$ICno         = dlookup("userdetails", "newIC", "userID=" . $userID);
$mobileNo     = dlookup("userdetails", "mobileNo", "userID=" . $userID);
$bondNo       = dlookup("loandocs", "rnoBond", "loanID=" . $loanid);
$tenure       = dlookup("loans", "loanPeriod", "loanID=" . $loanid);
$profit_rate  = dlookup("loans", "kadar_u", "loanID=" . $loanid); 
$channel = 1;

$redirectlink = 'https://app.ikoop.com.my/staging/index.php?vw=komoditi_list&mn=907';

$url = "https://rcbdev.assidqlink.com/rcb/webservice/rest/DoTrading.php";

$data = array(
    'ref_no'=> $bondNo,
    'financing_amount'=> $amount,
    'selling_price'=> $amount,
    'cust_name'=> $Name,
    'cust_id'=> $ICno,    
    'profit_rate'=> $profit_rate, 
    'tenure'=> $tenure,  
    'mobile_no'=> $mobileNo, 
    'channel'=> ".$channel."
);

$jsonData = json_encode($data);

$ch = curl_init();

$headers = array(
    "Accept:application/json",
    "Content-Type: application/json",
    "Authorization: Bearer 1@@%csjnd!n9xMNvLB8S2",
);


// echo $jsonData;

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0); 
curl_setopt($ch, CURLOPT_POSTFIELDS,$jsonData); 
curl_setopt($ch, CURLOPT_POST, true);
$output = curl_exec($ch);


$response   = json_decode($output,true);
$redirect   =$response["data"]["ref_no"];
$bills_no   =$response["data"]["status_code"];
$status_code=$response["data"]["status_desc"];

$updatedBy  = get_session("Cookie_userName");
$updatedDate = date("Y-m-d H:i:s"); 

// if ($output === false) {
//     echo 'cURL Error: ' . curl_error($ch);
// } else {
//     echo 'Response from the API:';
//     echo $output;
// }

if(!$response){
  echo "Transaksi tidak dapat dijalankan, sila tunggu sebentar dan cuba kembali selepas ini. Maaf atas kesulitan dan sekian terima kasih.";
}else{

  $sSQL  = "INSERT INTO komoditi (
       " ."no_sijil,
       " ."userID,
       " ."loanID,
       " ."amount,
       " ."tarikh_beli,
       " ."masa_beli
        )"." VALUES (
       ".tosql($bondNo, "Text").",
       ".tosql($userID, "Text").",
       ".tosql($loanid, "Text").",
       ".tosql($amount, "Number").",       
       ".tosql($updatedDate, "Text") . ", 
       ".tosql($masa_beli, "Text").")";
   $rs = &$conn->Execute($sSQL);

  print '<script>
          alert ("Ia akan pergi ke muka seterusnya.")';
         header("Location: ".$redirectlink);
  print'</script>';
}

curl_close($ch);
ob_end_flush();
?>