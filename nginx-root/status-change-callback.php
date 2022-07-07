<?php

// Load Sprint functions.
require_once "/var/www/ghost/system/secure/sprint-tools.php";
require_once "/var/www/ghost/system/secure/sprint-configuration.php";

// Tell Google to get lost.
header("X-Robots-Tag: noindex, nofollow", true);

// Make sure only post requests are accepted.
if ($_SERVER["REQUEST_METHOD"] != "POST") {
  header("Location: https://www.sidelinesprint.com/", true, 303);
  exit();
}

// Wrap in try block.
try {

  // Capture data from Campaign Monitor.
  $cm_json = file_get_contents('php://input');
  $cm_data = json_decode($cm_json, true);

  // Verify list ID matches main subscriber list ID
  if ($cm_data["ListID"] == $main_subscriber_list_id) {

    // Push unsubscribe updates to database.
    foreach ($cm_data["Events"] as $event) {

      // Parse email.
      $email = $event["EmailAddress"];

      // Send alert to unsubscribe from Beehiiv.
      server_alert($email . " unsubscribed from Campaign Monitor; remove from Beehiiv.");

    }
  }

} catch (Exception $e) {

  // Give error code.
  server_alert($e->getMessage());
  http_response_code(500);
  exit();

}
