<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://rcbdev.assidqlink.com/rcb/webservice/rest/DoTrading.php");

$headers = array(
    "Accept:application/json",
    "Content-Type: application/json",
    "Authorization: Bearer 1@@%csjnd!n9xMNvLB8S2",
);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // This disables SSL certificate verification
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
curl_close($ch);

echo $result;
?>