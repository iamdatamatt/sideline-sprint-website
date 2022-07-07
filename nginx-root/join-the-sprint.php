<?php

// Load package.
require_once "/var/www/ghost/system/secure/sprint-tools.php";

// Tell Google to get lost.
header("X-Robots-Tag: noindex, nofollow", true);

// Make sure only get requests are accepted.
if ($_SERVER["REQUEST_METHOD"] != "GET") {
  header("Location: https://www.sidelinesprint.com/", true, 303);
  exit();
}

// Make sure email set.
if (!isset($_GET["email"])) {
  header("Location: https://www.sidelinesprint.com/", true, 303);
  exit();
}

// Get and fix email.
$cleaned_email = clean_email($_GET["email"]);
if ($cleaned_email["is_valid"]) {
  $signup_email = $cleaned_email["email"];
} else {
  header("Location: https://www.sidelinesprint.com/", true, 303);
  exit();
}
$signup_ip_address = strval($_SERVER["REMOTE_ADDR"]) ?? NULL;
$signup_source = "one-click-link";
$signup_parameters = http_build_query($_GET) ?? NULL;
if (isset($_SERVER['HTTP_REFERER'])) {
  $signup_referrer_header = strval($_SERVER['HTTP_REFERER']) ?? NULL;
} else {
  $signup_referrer_header = NULL;
}
if (isset($_SERVER['HTTP_USER_AGENT'])) {
  $signup_user_agent = strval($_SERVER['HTTP_USER_AGENT']) ?? NULL;
} else {
  $signup_user_agent = NULL;
}
$signup_base_url = "https://www.sidelinesprint.com/join-the-sprint";

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
