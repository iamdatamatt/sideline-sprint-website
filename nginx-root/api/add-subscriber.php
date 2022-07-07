<?php

// Load package and keys.
require_once "/var/www/ghost/system/secure/sprint-tools.php";
require_once "/var/www/ghost/system/secure/sprint-configuration.php";

// Tell Google to get lost.
header("X-Robots-Tag: noindex, nofollow", true);

// Make sure only post requests are accepted.
if ($_SERVER["REQUEST_METHOD"] != "POST") {
  http_response_code(405);
  exit();
}

// Check for API key.
if (!isset($_POST["x-sprint-key"])) {
  http_response_code(401);
  exit();
}

// Validate API key.
$api_key = $_POST["x-sprint-key"];
if (!array_key_exists($api_key, $api_auth_array)) {
  http_response_code(401);
  exit();
}

// Make sure email is sent.
if (!isset($_POST["email"])) {
  http_response_code(400);
  exit();
}

// Get email from POST variables.
$cleaned_email = clean_email($_POST["email"]);
if ($cleaned_email["is_valid"]) {
  $signup_email = $cleaned_email["email"];
} else {
  http_response_code(400);
  exit();
}
$signup_ip_address = strval($_SERVER["REMOTE_ADDR"]) ?? NULL;
$signup_parameters = $api_auth_array[$api_key];
$signup_source = "api";

// Subscribe user.
$result = subscribe($signup_email,
                    $signup_ip_address,
                    $signup_source,
                    $signup_parameters);

// Set flow based on results.
if ($result["status"] == "success") {

  http_response_code(200);
  exit();

} elseif ($result["status"] == "unconfirmed") {

  http_response_code(200);
  exit();

} elseif ($result["status"] == "already_exists") {

  http_response_code(200);
  exit();

} elseif ($result["status"] == "banned") {

  http_response_code(200);
  exit();

} elseif ($result["status"] == "failure") {

  http_response_code(500);
  exit();

} else {

  http_response_code(500);
  exit();

}
