<?php

// Post URL
$postURL = "https://eboy.gr/wp";
// The Secret key
$secretKey = "5afd5ed9ba1853.54896312";
$firstname = "John";
$lastname = "";
$email = "";

// prepare the data
$data = array ();
$data['secret_key'] = $secretKey;
$data['slm_action'] = 'slm_create_new';
$data['first_name'] = $firstname;
$data['last_name'] = $lastname;
$data['email'] = $email;

// send data to API post URL
$ch = curl_init ($postURL);
curl_setopt ($ch, CURLOPT_POST, true);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
$returnValue = curl_exec ($ch);

// Process the return values
//var_dump($returnValue);
