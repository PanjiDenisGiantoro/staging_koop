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
  $paymentName = "Pokok (Saham)";

//////token//////
// $tokenUrl = "https://stg-identity.swittlelab.com/core/connect/token";

// $postFields = http_build_query([
//     'grant_type' => 'password',           // assuming password grant type
//     'client_id' => 'YOUR_CLIENT_ID',      // put your client_id here
//     'client_secret' => 'YOUR_CLIENT_SECRET',  // put your client_secret here
//     'username' => 'YOUR_USERNAME',        // your API username
//     'password' => 'YOUR_PASSWORD',        // your API password
//     'scope' => 'YOUR_SCOPE'                // optional, depends on your API config
// ]);

$Name         = dlookup("users", "name", "userID=" . $memberID);
$ICno         = dlookup("userdetails", "newIC", "userID=" . $memberID);
$email        = dlookup("users", "email", "userID=" . $memberID);
$mobileNo     = dlookup("userdetails", "mobileNo", "userID=" . $memberID);
$address      = dlookup("userdetails", "address", "userID=" . $memberID);
$caj          = 1;
$redirectlink = 'https://app.ikoop.com.my/stagingtri/getApi.php';
$amountcaj    = ($amount + $caj);

//bill reference kena unique. (kiv)

// $ch = curl_init($tokenUrl);

// $tokenResponse = curl_exec($ch);
// if ($tokenResponse === false) {
//     die("Token request failed: " . curl_error($ch));
// }
// curl_close($ch);

// $tokenData = json_decode($tokenResponse, true);
// if (!isset($tokenData['access_token'])) {
//     die("Failed to get access token. Response: " . $tokenResponse);
// }

// $accessToken = $tokenData['access_token'];

$recordBillUrl = "https://stg-integrationapi.swittlelab.com/api/Integration/RecordBill";

$data = array(
  "ActivityTag" => "RecordBill",
  "LanguageCode" => "en",
  "AppReleaseId" => "34",
  "GMTTimeDifference" => 8,
  "PaymentTypeId" => 6619,
  "BillReference" => 131,
  "BatchName" => null,
  "NetAmount" => $amountcaj,
  "BillAttributes" => array(
      array(
          "PaymentTypeSettingsTypeTag" => "CUSTOMER_NAME",
          "Value" => $Name
      ),
      array(
          "PaymentTypeSettingsTypeTag" => "CUSTOMER_MOBILE_NUMBER",
          "Value" => $mobileNo
      ),
      array(
          "PaymentTypeSettingsTypeTag" => "CUSTOMER_EMAIL_ADDRESS",
          "Value" => $email
      )
  )
);

$headers = array(
    "Accept:application/json",
    "Content-Type: application/json",
    "Authorization: Bearer a48d220c45999d37c870b2b76a256189", //kena refresh tiap masa
);

$ch = curl_init($recordBillUrl);
curl_setopt($ch, CURLOPT_URL, $recordBillUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0); 
curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data,true)); 

$output = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
    curl_close($ch);
    exit; // Stop the process if curl fails
}

curl_close($ch);

$response = json_decode($output, true);

// Check structure and extract ShortcutLink + BillId
if (isset($response["ShortcutLink"]) && isset($response["BillId"])) {
    $redirect = $response["ShortcutLink"];
    $bills_no = $response["BillId"];
} else {
    echo "Unexpected response structure:\n";
    print_r($response);
    exit;
}

// Proceed to insert into database
if (!$response) {
    echo "Transaksi tidak dapat dijalankan, sila tunggu sebentar dan cuba kembali selepas ini. Maaf atas kesulitan dan sekian terima kasih.";
    exit;
} else {
    $sSQL = "INSERT INTO resitonline (
                no_resit, 
                tarikh_resit, 
                bayar_kod, 
                amount, 
                bayar_nama, 
                createdDate, 
                createdBy, 
                updatedDate, 
                updatedBy, 
                statusRP,
                swittleurl
            ) VALUES (
                '". $bills_no ."',
                '". $date ."',
                '". $paymentID ."',
                '". $amount ."',
                '". $memberID ."',
                '". $date ."',
                '". $memberID ."',
                '". $updatedDate ."',
                '". $updatedBy ."',
                '0',
                '". $redirect ."'
            )";

    $rs = &$conn->Execute($sSQL);

    if (!$rs) {
        echo "Gagal rekod ke database. Sila hubungi pentadbir.";
        exit;
    }

    // ✅ Only redirect after DB insert is successful
    header("Location: $redirect");
    exit;
}

curl_close($ch);
ob_end_flush();
?>