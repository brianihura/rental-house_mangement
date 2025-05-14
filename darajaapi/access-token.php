<?php
$consumerKey = "9JKG7jUhVUmfxgM4ytIxZ03vu3BGZXO1032ouASt0BDwwoJd";
$consumerSecret = "xgFppGoXmXwOReWrtB9OxMAlhSWJuAbZuYnHet1J1Whel0ONfj139TwR4pGUmAAG";
$credentials = base64_encode("$consumerKey:$consumerSecret");

$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response);
echo $result->access_token;
