<?php
/**
 * FOR REFERENCE ONLY - THIS CODE IS NOT USED
 *
 * ALL OF THE BELOW CODE WAS PROVIDED BY TRUSTPILOT AND IS COPY/PASTED HERE FOR REFERENCE
 */

 // Get data from url
$firstname=$_GET['firstname'];
$lastname=$_GET['lastname'];
$email=$_GET['email'];
$clientnumber=$_GET['clientnumber'];
$name=($firstname.' '.$lastname);

 // To get the keys, base64 decode the keys you copy from the Trustpilot site:
    require_once('authenticatedencryption.php');
    $encrypt_key = base64_decode('...');
    $auth_key = base64_decode('...');

    // The payload should be a JSON object with your order data:
    $payload = [
    'email' => $email,
    'name' => $name,
    'ref' => $clientnumber,
    ];

    $payload = json_encode($payload);
    $trustpilot = new Trustpilot;
    $encryptedData = $trustpilot-> {'encryptPayload'}($payload, $encrypt_key, $auth_key);
    $trustpilot_invitation_link = "xyz?p=" . $encryptedData;

    // redirect user to encrypted link
    header("Location: $trustpilot_invitation_link");
?>


