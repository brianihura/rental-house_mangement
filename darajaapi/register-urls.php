<?php
// Consumer credentials
$consumerKey = "9JKG7jUhVUmfxgM4ytIxZ03vu3BGZXO1032ouASt0BDwwoJd";
$consumerSecret = "xgFppGoXmXwOReWrtB9OxMAlhSWJuAbZuYnHet1J1Whel0ONfj139TwR4pGUmAAG";

// Step 1: Get access token
$credentials = base64_encode("$consumerKey:$consumerSecret");

$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response);
$accessToken = $result->access_token;

// Step 2: Register C2B URLs
$shortcode = "60000"; // Sandbox Paybill

$confirmationUrl = "https://963c-197-248-89-21.ngrok-free.app/callback.php";
$validationUrl   = "https://963c-197-248-89-21.ngrok-free.app/validation.php";

$registerUrl = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $registerUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
]);

$data = [
    "ShortCode" => $shortcode,
    "ResponseType" => "Completed",
    "ConfirmationURL" => $confirmationUrl,
    "ValidationURL" => $validationUrl
];

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

echo $response;
?>
