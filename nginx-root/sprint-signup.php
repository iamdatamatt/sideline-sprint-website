<?php

// Load package.
require_once "/var/www/ghost/system/secure/sprint-tools.php";

// Tell Google to get lost.
header("X-Robots-Tag: noindex, nofollow", true);

// Make sure only post requests are accepted.
if ($_SERVER["REQUEST_METHOD"] != "POST") {
  header("Location: https://www.sidelinesprint.com/", true, 303);
  exit();
}

// Make sure only emails set.
if (!isset($_POST["email"])) {
  header("Location: https://www.sidelinesprint.com/", true, 303);
  exit();
}

// Get email from POST variables.
$cleaned_email = clean_email($_POST["email"]);
if ($cleaned_email["is_valid"]) {
  $signup_email = $cleaned_email["email"];
} else {
  header("Location: https://www.sidelinesprint.com/error", true, 303);
  exit();
}
$signup_ip_address = strval($_SERVER["REMOTE_ADDR"]) ?? NULL;
$signup_source = $_POST["signup_source"] ?? NULL;
$signup_parameters = $_POST["signup_params"] ?? NULL;
$signup_referrer_header = empty_to_null($_POST["referrer"] ?? NULL);
$signup_user_agent = empty_to_null($_POST["user_agent"] ?? NULL);
$signup_base_url = empty_to_null($_POST["base_url"] ?? NULL);

// Subscribe user.
$result = subscribe(
  $signup_email,
  $signup_ip_address,
  $signup_source,
  $signup_parameters,
  $signup_referrer_header,
  $signup_user_agent,
  $signup_base_url
);

// Set flow based on results.
if ($result["status"] == "success") {

  header("Location: https://www.sidelinesprint.com/confirmed", true, 303);
  exit();

} elseif ($result["status"] == "unconfirmed") {

  header("Location: https://www.sidelinesprint.com/confirmed", true, 303);
  exit();

} elseif ($result["status"] == "already_exists") {

  header("Location: https://www.sidelinesprint.com/confirmed", true, 303);
  exit();

} elseif ($result["status"] == "banned") {

  header("Location: https://www.sidelinesprint.com/", true, 303);
  exit();

} elseif ($result["status"] == "failure") {

  header("Location: https://www.sidelinesprint.com/error", true, 303);
  exit();

} else {

  header("Location: https://www.sidelinesprint.com/", true, 303);
  exit();

}
