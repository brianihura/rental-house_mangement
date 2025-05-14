<?php
$accessToken = "YOUR_ACCESS_TOKEN"; // Generate via access_token.php

$url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
]);

$data = [
    "ShortCode" => "600000",
    "CommandID" => "CustomerPayBillOnline",
    "Amount" => 500,
    "Msisdn" => "254708374149", // Use this test number only
    "BillRefNumber" => "HOUSE123" // This must match tenant.account_number
];

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

echo $response;
